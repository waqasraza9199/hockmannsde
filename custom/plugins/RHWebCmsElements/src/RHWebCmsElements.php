<?php declare(strict_types=1);

namespace RHWeb\CmsElements;

use RHWeb\CmsElements\Core\PluginHelpers;
use Shopware\Core\Framework\Plugin;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class RHWebCmsElements extends Plugin
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/Content/DependencyInjection'));
        $loader->load('manufacturer.xml');
        $loader->load('media.xml');
    }

    public function uninstall(UninstallContext $context): void
    {
        parent::uninstall($context);

        if ($context->keepUserData()) {
            return;
        }

        PluginHelpers::removeCmsBlocks($this->container, $context->getContext(), [
            'rhweb-brand-slider',
            'rhweb-cta-banner-basic',
            'rhweb-cta-banner-category-five-columns',
            'rhweb-cta-banner-category-four-columns',
            'rhweb-cta-banner-category-three-columns',
            'rhweb-cta-banner-category-two-columns',
            'rhweb-cta-banner-video',
            'rhweb-multi-slider-five',
            'rhweb-multi-slider-four',
            'rhweb-multi-slider-three',
            'rhweb-multi-slider-two',
            'rhweb-service-area-service-area-service-area-four-columns',
            'rhweb-service-area-service-area-service-area-three-columns',
            'rhweb-service-area-service-area-service-area-two-columns',
            'rhweb-service-area-service-area-service-area-one-column',
            'rhweb-testimonial-four-column',
            'rhweb-testimonial-three-column',
            'rhweb-testimonial-two-column',
            'rhweb-testimonial-one-column',
            'rhweb-testimonial-five-slider',
            'rhweb-testimonial-four-slider',
            'rhweb-testimonial-three-slider',
            'rhweb-testimonial-two-slider',
            'rhweb-shop-the-look',
            'rhweb-deal-counter'
        ]);

        PluginHelpers::removeCmsSlots($this->container, $context->getContext(), [
            'rhweb-brand-slider',
            'rhweb-cta-banner',
            'rhweb-testimonial',
            'rhweb-shop-the-look',
            'rhweb-deal-counter'
        ]);
    }
}
