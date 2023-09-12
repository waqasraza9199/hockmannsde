<?php

declare(strict_types=1);

namespace WebLa_IntelligentCrossSelling\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1649663958Initialisation extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1649663958;
    }

    public function update(Connection $connection): void
    {
        $query = '
            CREATE TABLE `webla_intelligent_cross_selling_property_group` (
                `id` BINARY(16) NOT NULL,
                `property_group_id` BINARY(16) NOT NULL,
                `weight` INT(11) NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`),
                CONSTRAINT FKPropertyGroup FOREIGN KEY (property_group_id) REFERENCES property_group (id) ON UPDATE CASCADE ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ';
        $connection->executeStatement($query);
    }

    public function updateDestructive(Connection $connection): void
    {
        $query = '
            DROP TABLE IF EXISTS `webla_intelligent_cross_selling_property_group`;
        ';
        $connection->executeStatement($query);

        $this->update($connection);
    }
}
