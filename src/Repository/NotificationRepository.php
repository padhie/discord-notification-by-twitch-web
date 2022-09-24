<?php

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function saveEntity(Notification $notification): void
    {
        $this->getEntityManager()->persist($notification);
        $this->getEntityManager()->flush();
    }

    /**
     * @param int[] $ids
     */
    public function deleteByIds(array $ids): void
    {
        if (count($ids) === 0) {
            return;
        }

        $this->getEntityManager()
            ->getConnection()
            ->executeQuery(
                <<<'SQL'
                    DELETE FROM notifications
                    WHERE id IN (:ids)
                    SQL,
                ['ids' => $ids],
                ['ids' => Connection::PARAM_INT_ARRAY]
            );
    }

    /**
     * @param int[] $ids
     */
    public function deleteForUserByIds(int $userId, array $ids): void
    {
        if (count($ids) === 0) {
            return;
        }

        $this->getEntityManager()
            ->getConnection()
            ->executeQuery(
                <<<'SQL'
                    DELETE FROM notifications
                    WHERE user_id = :userId
                        AND id IN (:ids)
                    SQL,
                [
                    'ids' => $ids,
                    'userId' => $userId,
                ],
                [
                    'ids' => Connection::PARAM_INT_ARRAY,
                    'userId' => ParameterType::INTEGER,
                ]
            );
    }
}
