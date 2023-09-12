<?php declare(strict_types=1);

namespace Nimbits\NimbitsArticleQuestionsNext;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\CustomField\CustomFieldTypes;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;
use Shopware\Core\System\SystemConfig\SystemConfigDefinition;
use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Defaults;

class NimbitsArticleQuestionsNext extends Plugin
{

	public function getViewPaths(): array
    {
        $viewPaths = parent::getViewPaths();
        $viewPaths[] = 'Resources/views/storefront';

        return $viewPaths;
    }

	public function activate(ActivateContext $context): void
    {
        parent::activate($context);
    }

	public function deactivate(DeactivateContext $context): void
    {
        $shopwareContext = $context->getContext();

        parent::deactivate($context);
    }

	public function install(InstallContext $context): void
    {
		parent::install($context);
    }

	public function uninstall(UninstallContext $context): void
    {
        parent::uninstall($context);

        if($context->keepUserData()){
            return;
        }

        $connection = $this->container->get(Connection::class);
		$connection->executeStatement('DROP TABLE IF EXISTS `nimbits_articlequestions`');

		//shopowner mail loeschen
		$sql = "DELETE mail_template_type.*, mail_template_type_translation.*, mail_template_translation.*, mail_template.*
				FROM mail_template_type
				JOIN mail_template_type_translation 
					ON mail_template_type_translation.mail_template_type_id = mail_template_type.id
				JOIN mail_template 
					ON mail_template.mail_template_type_id = mail_template_type.id
				JOIN mail_template_translation 
					ON mail_template_translation.mail_template_id = mail_template.id
				WHERE mail_template_type.technical_name = 'nimbits_aq_emailshopowner'";

		$connection->executeStatement($sql);


		//customer request mail loeschen
		$sql = "DELETE mail_template_type.*, mail_template_type_translation.*, mail_template_translation.*, mail_template.*
				FROM mail_template_type
				JOIN mail_template_type_translation 
					ON mail_template_type_translation.mail_template_type_id = mail_template_type.id
				JOIN mail_template 
					ON mail_template.mail_template_type_id = mail_template_type.id
				JOIN mail_template_translation 
					ON mail_template_translation.mail_template_id = mail_template.id
				WHERE mail_template_type.technical_name = 'nimbits_aq_emailcustomer'";

		$connection->executeStatement($sql);

		//customer answer mail loeschen
		$sql = "DELETE mail_template_type.*, mail_template_type_translation.*, mail_template_translation.*, mail_template.*
				FROM mail_template_type
				JOIN mail_template_type_translation 
					ON mail_template_type_translation.mail_template_type_id = mail_template_type.id
				JOIN mail_template 
					ON mail_template.mail_template_type_id = mail_template_type.id
				JOIN mail_template_translation 
					ON mail_template_translation.mail_template_id = mail_template.id
				WHERE mail_template_type.technical_name = 'nimbits_aq_emailcustomeranswer'";

		$connection->executeStatement($sql);


		$sql = "DELETE FROM mail_template WHERE mail_template.mail_template_type_id IS NULL";
		$connection->executeStatement($sql);
    }


}