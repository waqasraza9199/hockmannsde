<?php declare(strict_types=1);

namespace Lenz\GoogleShopping\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\{MigrationStep};


class Migration1581953540GoogleShoppingTaxonomy extends MigrationStep {

    public function getCreationTimestamp(): int
    {
        return 1581953540;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `lenz_google_shopping_taxonomy` (
                `id` BINARY(16) NOT NULL,
                `cat_id` INT(11) NOT NULL UNIQUE ,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // Nothing
    }


}
