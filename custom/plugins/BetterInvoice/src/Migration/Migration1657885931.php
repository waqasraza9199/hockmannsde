<?php declare(strict_types=1);

namespace LZYT8\BetterInvoice\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\InheritanceUpdaterTrait;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1657885931 extends MigrationStep
{
    use InheritanceUpdaterTrait;

    /**
     * {@inheritDoc}
     */
    public function getCreationTimestamp(): int
    {
        return 1657885931;
    }

    /**
     * {@inheritDoc}
     */
    public function update(Connection $connection): void
    {
        $query = '
            CREATE TABLE IF NOT EXISTS `lzyt_custom_drop` (
                `id` BINARY(16) NOT NULL,
                `enabled` TINYINT(1) NOT NULL,
                `priority` INT NOT NULL,
                `bank` VARCHAR(200) NOT NULL,
                `iban` VARCHAR(200) NOT NULL,
                `bic` VARCHAR(200) NOT NULL,
                `rule_id` BINARY(16) NOT NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3),
                PRIMARY KEY (`id`),
                CONSTRAINT `fk.dvsn_category_promotion_discount.rule_id` FOREIGN KEY (`rule_id`)
                    REFERENCES `rule` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            )
            ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
        ';

        $connection->executeStatement($query);
    }

    /**
     * {@inheritDoc}
     */
    public function updateDestructive(Connection $connection): void { }
}