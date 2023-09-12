<?php declare(strict_types=1);

namespace Nimbits\NimbitsArticleQuestionsNext\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1672846356ArticleQuestionsFixOldQuestionsForVersionFields extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1672846355;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
UPDATE nimbits_articlequestions,product SET nimbits_articlequestions.product_version_id = product.version_id,  nimbits_articlequestions.version_id = product.version_id  
WHERE nimbits_articlequestions.article_id = product.id AND nimbits_articlequestions.product_version_id IS NULL AND nimbits_articlequestions.version_id IS NULL
SQL;

        $connection->executeStatement($sql, []);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
