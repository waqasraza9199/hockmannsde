<?php declare(strict_types=1);

namespace Nimbits\NimbitsArticleQuestionsNext\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1629896198ArticleQuestion extends MigrationStep
{
    //25.08.2021
    public function getCreationTimestamp(): int
    {
        return 1629896198;
    }

    public function update(Connection $connection): void
    {
        //add language_id to database table

        $sql = <<<SQL
ALTER TABLE `nimbits_articlequestions`
ADD COLUMN `language_id` BINARY(16) NULL
SQL;

        $connection->executeStatement($sql, []);
    }

    public function updateDestructive(Connection $connection): void
    {

    }

}