<?php

declare(strict_types=1);

namespace WebLa_IntelligentCrossSelling;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Doctrine\DBAL\Connection;

class WebLa_IntelligentCrossSelling extends Plugin
{
    public function install(InstallContext $context): void
    {
        parent::install($context);
    }

    // postInstall()
    public function update(UpdateContext $context): void
    {
        parent::update($context);
    }

    // postUpdate()
    public function activate(ActivateContext $context): void
    {
        parent::activate($context);
    }

    public function deactivate(DeactivateContext $context): void
    {
        parent::deactivate($context);
    }

    public function uninstall(UninstallContext $context): void
    {
        if ($context->keepUserData()) {
            parent::uninstall($context);
            return;
        }

        $connection = $this->container->get(Connection::class);

        $connection->executeStatement('DROP TABLE IF EXISTS `webla_intelligent_cross_selling_property_group`;');
        $connection->executeStatement('DROP TABLE IF EXISTS `webla_intelligent_cross_selling_settings`;');
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        // $container->addCompilerPass(new CustomPass());
    }
}
