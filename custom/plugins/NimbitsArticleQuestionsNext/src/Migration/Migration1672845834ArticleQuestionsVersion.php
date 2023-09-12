<?php declare(strict_types=1);

namespace Nimbits\NimbitsArticleQuestionsNext\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1672845834ArticleQuestionsVersion extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1672845834;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
ALTER TABLE `nimbits_articlequestions`
ADD COLUMN `version_id` BINARY(16) NULL
SQL;

        $connection->executeStatement($sql, []);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
