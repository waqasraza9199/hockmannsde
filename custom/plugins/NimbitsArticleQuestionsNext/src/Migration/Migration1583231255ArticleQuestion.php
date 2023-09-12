<?php declare(strict_types=1);

namespace Nimbits\NimbitsArticleQuestionsNext\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1583231255ArticleQuestion extends MigrationStep
{
    //03.03.2020
    public function getCreationTimestamp(): int
    {
        return 1583231255;
    }

    public function update(Connection $connection): void
    {
        $sql = "
			CREATE TABLE IF NOT EXISTS `nimbits_articlequestions` (
				`id` BINARY(16) NOT NULL,
				`article_id` BINARY(16) NOT NULL,
				`salutation` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
				`firstname` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
				`surname` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
				`mail` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
				`company` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
				`question` TEXT COLLATE utf8mb4_unicode_ci,
				`answer` TEXT COLLATE utf8mb4_unicode_ci NULL,
				`active` INT(1) DEFAULT 0, 
				`additional_info` json NULL,
				`created_at` DATETIME(3) NOT NULL,
				`updated_at` DATETIME(3),
				PRIMARY KEY (`id`),
				CONSTRAINT `fk.nimbits_articlequestions.article_id` FOREIGN KEY (`article_id`)
					REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
			)
				ENGINE = InnoDB
				DEFAULT CHARSET = utf8mb4
				COLLATE = utf8mb4_unicode_ci;
			";
        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {

    }
}