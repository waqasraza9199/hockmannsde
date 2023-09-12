<?php declare(strict_types=1);

namespace WebLa_IntelligentCrossSelling\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1668700155AddCategorySwitch extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1668700155;
    }

    public function update(Connection $connection): void
    {
        $query = '
            ALTER TABLE `webla_intelligent_cross_selling_settings`
            ADD `only_category` tinyint(1) NULL AFTER `active`;
        ';
        $connection->executeStatement($query);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
