<?php declare(strict_types=1);

namespace LZYT\Enev\Migration;

use LZYT\Enev\Core\Content\Enev\EnevDefinition;
use Shopware\Core\Framework\Migration\MigrationStep;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;

class Migration1649954390 extends MigrationStep {
    /**
     * get creation timestamp
     */
    public function getCreationTimestamp(): int {
        return 1649954390;
    }

    /**
     * update non-destructive changes
     *
     * @param Connection $connection
     *
     * @throws DBALException
     */
    public function update(Connection $connection): void {
        $connection->executeStatement('CREATE TABLE IF NOT EXISTS `'. EnevDefinition::ENTITY_NAME.'` (
              `id` binary(16) NOT NULL,
              `version_id` binary(16) NOT NULL,
              `product_id` binary(16) DEFAULT NULL,
              `active` tinyint(1) NOT NULL DEFAULT 0,
              `position` int(11) DEFAULT 1,
              `class` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
              `spectrum_from` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `spectrum_to` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `color` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
              `media_id` binary(16) DEFAULT NULL,
              `datasheet_id` binary(16) DEFAULT NULL,
              `icon_id` binary(16) DEFAULT NULL,
              `created_at` datetime(3) NOT NULL,
              `updated_at` datetime(3) DEFAULT NULL,
              PRIMARY KEY (`id`,`version_id`),
              KEY `fk.lzyt_enev.product_id` (`product_id`),
              KEY `fk.lzyt_enev.media_id` (`media_id`),
              KEY `fk.lzyt_enev.datasheet_id` (`datasheet_id`),
              KEY `fk.lzyt_enev.icon_id` (`icon_id`),
              CONSTRAINT `fk.lzyt_enev.product_id` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
              CONSTRAINT `fk.lzyt_enev.media_id` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
              CONSTRAINT `fk.lzyt_enev.datasheet_id` FOREIGN KEY (`datasheet_id`) REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
              CONSTRAINT `fk.lzyt_enev.icon_id` FOREIGN KEY (`icon_id`) REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    /**
     * update destructive changes
     *
     * @param Connection $connection
     */
    public function updateDestructive(Connection $connection): void {
    }
}