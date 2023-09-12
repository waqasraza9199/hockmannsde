<?php

declare(strict_types=1);

namespace WebLa_IntelligentCrossSelling\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1650545240UpdateSettingsTitle extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1650545240;
    }

    public function update(Connection $connection): void
    {
        $query = '
            ALTER TABLE `webla_intelligent_cross_selling_settings`
            ADD `show_title` tinyint(1) NULL AFTER `active`;
        ';
        $connection->executeStatement($query);
    }

    public function updateDestructive(Connection $connection): void
    {
        $query = '
            ALTER TABLE `webla_intelligent_cross_selling_settings`
            DROP `show_title`;  
        ';
        $connection->executeStatement($query);
        $this->update($connection);
    }
}
