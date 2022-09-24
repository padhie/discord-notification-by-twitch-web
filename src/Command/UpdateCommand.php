<?php

namespace App\Command;

use App\Service\Updater\Streamer\StreamerUpdater;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class UpdateCommand extends Command
{
    protected static $defaultName = 'app:update';

    private StreamerUpdater $streamerUpdater;

    public function __construct(StreamerUpdater $streamerUpdater)
    {
        parent::__construct(null);

        $this->streamerUpdater = $streamerUpdater;
    }

    protected function configure(): void
    {
        $this->setDescription('Update streamer structs to next version');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->streamerUpdater->update();
            $io->success('Struct updated');
        } catch (Exception $exception) {
            $io->error('Something went wrong: ' . $exception->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
