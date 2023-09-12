<?php declare(strict_types=1);

namespace WebLa_IntelligentCrossSelling\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1650453780UpdateSettings extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1650453780;
    }

    public function update(Connection $connection): void
    {
        $query = '
            CREATE TABLE `webla_intelligent_cross_selling_settings` (
                `id` BINARY(16) NOT NULL,
                `title` VARCHAR(255) NOT NULL,
                `max_products` INT(11) NULL,
                `active` TINYINT(1) NULL DEFAULT 0,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ';
        $connection->executeStatement($query);
    }

    public function updateDestructive(Connection $connection): void
    {
        $query = '
            DROP TABLE IF EXISTS `webla_intelligent_cross_selling_settings`;
        ';
        $connection->executeStatement($query);

        $this->update($connection);
    }
}
