<?php declare(strict_types=1);

namespace Lenz\GoogleShopping\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1594817911GoogleShoppingTaxonomyTranslation extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1594817911;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `lenz_google_shopping_taxonomy_translation` (
              `lenz_google_shopping_taxonomy_id` BINARY(16) NOT NULL,
              `language_id` BINARY(16) NOT NULL,
              `name` VARCHAR(255) NOT NULL,
              `created_at` DATETIME(3) NOT NULL,
              `updated_at` DATETIME(3) NULL,
              PRIMARY KEY (`lenz_google_shopping_taxonomy_id`, `language_id`),
              CONSTRAINT `fk.lenz_google_shopping_taxonomy_translation.language_id` FOREIGN KEY (`language_id`)
                REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
