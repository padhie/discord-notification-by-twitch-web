<?php

namespace App\Repository;

use App\Entity\Setting;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method Setting[] findAll()
 */
final class SettingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Setting::class);
    }

    public function getValueByKey(string $key): ?string
    {
        $setting = $this
            ->getEntityManager()
            ->getConnection()
            ->executeQuery('
                    SELECT `value`
                    FROM settings
                    WHERE `key` = :key
                ',
                ['key' => $key]
            )
            ->fetchFirstColumn();

        return count($setting) > 0
            ? $setting[0]
            : null;
    }

    public function findAsBool(string $key, bool $default = false): bool
    {
        $value = $this->getValueByKey($key);
        if ($value === null) {
            return $default;
        }

        return in_array($value, ['1', 'true', 'yes']);
    }

    public function findAsDateTime(string $key): ?DateTimeImmutable
    {
        $value = $this->getValueByKey($key);
        if ($value === null) {
            return null;
        }

        try {
            return new DateTimeImmutable($value);
        } catch (Exception $exception) {
            return null;
        }
    }
}
