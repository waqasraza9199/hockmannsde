<?php declare(strict_types=1);

namespace ZaibaNotifyAdminIfSMTPNotWorking;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class ZaibaNotifyAdminIfSMTPNotWorking extends Plugin
{
    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        if ($uninstallContext->keepUserData()) {
            return;
        }

        $systemConfigRepository = $this->container->get('system_config.repository');

        $configKeys = array(
            'ZaibaNotifyAdminIfSMTPNotWorking.config.smtpError',
            'ZaibaNotifyAdminIfSMTPNotWorking.config.smtpErrorMessage'
        );

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('configurationKey', $configKeys));
        $systemConfigIds = $systemConfigRepository->searchIds($criteria, Context::createDefaultContext())->getIds();
        if (empty($systemConfigIds)) {
            return;
        }

        $ids = array_map(static function ($id) {
            return ['id' => $id];
        }, $systemConfigIds);

        $systemConfigRepository->delete($ids, Context::createDefaultContext());
    }
}
