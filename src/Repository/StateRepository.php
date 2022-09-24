<?php

namespace App\Repository;

use App\Entity\State;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method State|null find($id, $lockMode = null, $lockVersion = null)
 * @method State|null findOneBy(array $criteria, array $orderBy = null)
 * @method State[]    findAll()
 * @method State[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, State::class);
    }

    public function getLastOnlineStateOfChannel(string $channel): bool
    {
        $state = $this->findOneBy([
            'channel' => $channel,
        ]);

        if (!$state instanceof State) {
            return false;
        }

        return $state->isOnline();
    }

    public function setOnlineStateOfChannel(string $channel, bool $isOnline): void
    {
        $state = $this->findOneBy([
            'channel' => $channel,
        ]);

        if ($state === null) {
            $this->addNewState($channel, $isOnline);

            return;
        }

        $this->getEntityManager()
            ->getConnection()
            ->executeQuery(
                <<<'SQL'
                    UPDATE `state`
                    SET online = :isOnline
                    WHERE channel = :channel
                    SQL,
                [
                    'channel' => $channel,
                    'isOnline' => $isOnline,
                ],
                [
                    'channel' => ParameterType::STRING,
                    'isOnline' => ParameterType::BOOLEAN,
                ]
            );
    }

    private function addNewState(string $channel, bool $isOnline): void
    {
        $this->getEntityManager()
            ->getConnection()
            ->executeQuery(
                <<<'SQL'
                    INSERT INTO `state`
                        (channel, online)
                    VALUES
                        (:channel, :isOnline)
                    SQL,
                [
                    'channel' => $channel,
                    'isOnline' => $isOnline,
                ],
                [
                    'channel' => ParameterType::STRING,
                    'isOnline' => ParameterType::BOOLEAN,
                ]
            );
    }
}
