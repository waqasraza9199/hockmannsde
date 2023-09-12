<?php declare(strict_types=1);

namespace LZYT\Enev;

use LZYT\Enev\Core\Content\Enev\EnevDefinition;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Doctrine\DBAL\Connection;

class LzytEnev extends Plugin
{
    /**
     * changing the storefront script path
     *
     * @return string
     */
    public function getStorefrontScriptPath(): string
    {
        return 'Resources/dist/storefront/js';
    }

    public function activate(ActivateContext $context): void
    {
        parent::activate($context);

        $connection = $this->getConnection();
        $connection->executeStatement('UPDATE `product_sorting` SET `active`=1 WHERE url_key LIKE "lzyt-enev-%"');
    }

    public function deactivate(DeactivateContext $context): void
    {
        parent::deactivate($context);

        $connection = $this->getConnection();
        $connection->executeStatement('UPDATE `product_sorting` SET `active`=0 WHERE url_key LIKE "lzyt-enev-%"');
    }

    /**
     * removing the plugin data
     *
     * @param UninstallContext $context
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function uninstall(UninstallContext $context): void
    {
        parent::uninstall($context);

        if ($context->keepUserData()) {
            return;
        }

        $connection = $this->getConnection();
        $connection->executeStatement('DROP TABLE IF EXISTS `' . EnevDefinition::ENTITY_NAME . '`');
        $connection->executeStatement('DELETE FROM `product_sorting` WHERE url_key LIKE "lzyt-enev-%"');
    }

    private function getConnection()
    {
        return $this->container->get(Connection::class);
    }
}
