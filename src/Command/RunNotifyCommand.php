<?php

namespace App\Command;

use App\Entity\Notification;
use App\Entity\Setting;
use App\Model\Statistic;
use App\Model\StreamerItem;
use App\Repository\NotificationRepository;
use App\Repository\SettingRepository;
use App\Repository\StateRepository;
use App\Service\DiscordService;
use App\Service\InfluxService;
use App\Service\TwitchService;
use DateTimeImmutable;
use Exception;
use Padhie\TwitchApiBundle\Response\Streams\Stream;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

final class RunNotifyCommand extends Command
{
    private const OPTION_CHANNELS = 'channels';
    private const OPTION_DRY_RUN = 'dry-run';
    private const OPTION_FORCE_NOTIFY = 'force-notify';

    protected static $defaultName = 'app:run-notify';

    private TwitchService $twitchService;
    private DiscordService $discordService;
    private InfluxService $influxService;
    private NotificationRepository $notificationRepository;
    private StateRepository $stateRepository;
    private SettingRepository $settingRepository;
    private LoggerInterface $logger;

    public function __construct(
        TwitchService $twitchService,
        DiscordService $discordService,
        InfluxService $influxService,
        NotificationRepository $notificationRepository,
        StateRepository $stateRepository,
        SettingRepository $settingRepository,
        LoggerInterface $logger
    ) {
        parent::__construct();

        $this->twitchService = $twitchService;
        $this->discordService = $discordService;
        $this->influxService = $influxService;
        $this->notificationRepository = $notificationRepository;
        $this->stateRepository = $stateRepository;
        $this->settingRepository = $settingRepository;
        $this->logger = $logger;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Check the online state of configure channel and push notify to discord')
            ->addOption(
                self::OPTION_CHANNELS,
                null,
                InputOption::VALUE_OPTIONAL,
                'list of comma separated channel to notify'
            )
            ->addOption(
                self::OPTION_DRY_RUN,
                null,
                InputOption::VALUE_OPTIONAL,
                'dont send notification or save state - influx push and notify will be count as success',
                false
            )
            ->addOption(
                self::OPTION_FORCE_NOTIFY,
                null,
                InputOption::VALUE_OPTIONAL,
                'force to push the notification',
                false
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (!$this->isActive()) {
            $io->info('NOT ACTIVE');

            return Command::SUCCESS;
        }

        $dryRun = (bool) ($input->getOption(self::OPTION_DRY_RUN) ?? true);
        $forceNotify = (bool) ($input->getOption(self::OPTION_FORCE_NOTIFY) ?? true);

        if ($dryRun === true && $forceNotify === true) {
            $io->error('dry-run and force-notify not allow together');

            return Command::INVALID;
        }

        $statistic = new Statistic();

        try {
            $channelList = $this->getChannelList($input);
            if (count($channelList) === 0) {
                $io->info('no ChannelList');

                return Command::SUCCESS;
            }

            $twitchStreams = $this->twitchService->getTwitchStreams($channelList);

            if ($twitchStreams->count() === 0) {
                $statistic->offline = $channelList;

                $io->info('no Stream Data');
                $this->logger->info('no Stream Data');

                return Command::SUCCESS;
            }

            $notifications = $this->notificationRepository->findBy([
                'channel' => $twitchStreams->getAllNames(),
            ]);

            foreach ($notifications as $notification) {
                $twitchStream = $twitchStreams->get($notification->getChannel());
                $this->runSingleNotification($notification, $statistic, $twitchStream, $dryRun, $forceNotify);
            }

            return Command::SUCCESS;
        } catch (Throwable $throwable) {
            $this->logger->error(sprintf(
                'Message: %s\nCode: %s\nFile: %s\nLine: %s',
                $throwable->getMessage(),
                $throwable->getCode(),
                $throwable->getFile(),
                $throwable->getLine()
            ));

            return Command::FAILURE;
        } finally {
            $this->updateStateByStatistic($statistic);
            $this->pushToInflux($statistic, $dryRun);
            $this->printStatistic($io, $statistic);
        }
    }

    private function isActive(): bool
    {
        $active = $this->settingRepository->findAsBool(Setting::NOTIFICATION_ACTIVE);
        if ($active === true) {
            return true;
        }

        $inactiveUntil = $this->settingRepository->findAsDateTime(Setting::NOTIFICATION_INACTIVE_UNTIL);
        if ($inactiveUntil === null) {
            return false;
        }

        return (new DateTimeImmutable())->format('Y-m-d') === $inactiveUntil->format('Y-m-d');
    }

    /**
     * @return string[]
     */
    private function getChannelList(InputInterface $input): array
    {
        $notifications = $this->notificationRepository->findAll();
        $allChannelNames = array_map(
            static fn (Notification $notification): string => $notification->getChannel(),
            $notifications
        );

        $inputChannel = $input->getOption(self::OPTION_CHANNELS);
        if ($inputChannel === null) {
            return $allChannelNames;
        }

        $channelList = explode(',', $inputChannel);
        $channelList = array_filter($channelList);

        return array_intersect($allChannelNames, $channelList);
    }

    private function updateStateByStatistic(Statistic $statistic): void
    {
        foreach ($statistic->online as $onlineChannel) {
            $this->stateRepository->setOnlineStateOfChannel($onlineChannel, true);
        }

        foreach ($statistic->offline as $offlineChannel) {
            $this->stateRepository->setOnlineStateOfChannel($offlineChannel, false);
        }
    }

    private function runSingleNotification(
        Notification $notification,
        Statistic $statistic,
        ?Stream $twitchStream,
        bool $dryRun,
        bool $forceNotify
    ): void {
        $channelName = $notification->getChannel();

        if ($twitchStream === null) {
            $statistic->offline[] = $channelName;

            return;
        }

        $statistic->online[] = $channelName;

        $onlineBefore = $this->stateRepository->getLastOnlineStateOfChannel($channelName);
        if ($onlineBefore === true) {
            $statistic->success[] = $channelName;

            if ($forceNotify === false) {
                return;
            }
        }

        $streamer = StreamerItem::createFromEntity($notification);
        try {
            if ($dryRun === false) {
                $this->discordService->executeForStream($streamer, $twitchStream);
            }

            $statistic->notified[] = $streamer->channel;
        } catch (Exception $exception) {
            $statistic->failed[] = $streamer->channel;
        }
    }

    private function pushToInflux(Statistic $statistic, bool $dryRun): void
    {
        foreach ($statistic->online as $online) {
            if (!$dryRun) {
                $this->influxService->push($online, 1);
            }

            $statistic->pushed[] = $online;
        }

        foreach ($statistic->offline as $offline) {
            if (!$dryRun) {
                $this->influxService->push($offline, 0);
            }

            $statistic->pushed[] = $offline;
        }
    }

    private function printStatistic(SymfonyStyle $io, Statistic $statistic): void
    {
        $io->writeln(sprintf(
            'Offline: %s',
            implode(',', $statistic->offline)
        ));

        $io->writeln(sprintf(
            'Online: %s',
            implode(',', $statistic->online)
        ));

        $io->writeln(sprintf(
            'Notified: %s',
            implode(',', $statistic->notified)
        ));

        $io->writeln(sprintf(
            'Pushed: %s',
            implode(',', $statistic->pushed)
        ));
    }
}
