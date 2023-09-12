<?php

namespace EmcgnNoVariantPreselection\Subscriber;

use EmcgnNoVariantPreselection\Component\CheckConfig;
use EmcgnNoVariantPreselection\Component\CheckId;
use EmcgnNoVariantPreselection\Component\ConfiguratorReset;
use EmcgnNoVariantPreselection\Component\HighestUnitPrice;
use EmcgnNoVariantPreselection\Component\MasterProduct;
use EmcgnNoVariantPreselection\Component\MasterProductMeta;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductPageSubscriber implements EventSubscriberInterface
{
    private MasterProduct $masterProduct;
    private MasterProductMeta $masterProductMeta;
    private CheckId $checkId;
    private CheckConfig $checkConfig;
    private ConfiguratorReset $configuratorReset;
    private HighestUnitPrice $highestUnitPrice;

    public static function getSubscribedEvents(): array
    {
        return [
            ProductPageLoadedEvent::class => ['onProductPage', 50]
        ];
    }

    public function __construct(MasterProduct $masterProduct, MasterProductMeta $masterProductMeta, CheckId $checkId, CheckConfig $checkConfig, ConfiguratorReset $configuratorReset, HighestUnitPrice $highestUnitPrice)
    {
        $this->masterProduct = $masterProduct;
        $this->masterProductMeta = $masterProductMeta;
        $this->checkId = $checkId;
        $this->checkConfig = $checkConfig;
        $this->configuratorReset = $configuratorReset;
        $this->highestUnitPrice = $highestUnitPrice;
    }

    /**
     * Loading the product page
     *
     * @param ProductPageLoadedEvent $event
     * @return void
     */
    public function onProductPage(ProductPageLoadedEvent $event)
    {
        // Read out product data
        $product = $event->getPage()->getProduct();
        $parentId = $product->getParentId();

        // If no variant article, then cancel
        if (empty($parentId)) {
            return;
        }

        $context = $event->getSalesChannelContext();
        $request = $event->getRequest();

        $masterProduct = $this->masterProduct->getMasterProduct($product, $context);
        $masterProduct = $this->masterProduct->getSeoCategory($masterProduct, $context);

        $comparedId = $this->checkId->compareId($request, $masterProduct);
        $noVariantPreselection = $this->checkConfig->getConfig($masterProduct);

        // If the compared Id is not the same and the plugin configuration is not active for the main product, then cancel.
        if ($comparedId == false and $noVariantPreselection != "active") {
            return;
        }

        // If the compared Id is not the same and the plugin configuration is active
        // get the url to the main product for the configurator reset button, then cancel.
        if ($comparedId == false and $noVariantPreselection == "active") {
            $resetUrl = $this->configuratorReset->getResetUrl($context, $masterProduct, $request);

            // Set Variable for Storefront
            $array = array(
                "resetUrl" => $resetUrl
            );
            $storefrontVariables = new ArrayStruct();
            $storefrontVariables->assign($array);
            $event->getPage()->getProduct()->addExtension('EmcgnNoVariantPreselection', $storefrontVariables);

            return;
        }

        // If the compared Id is the same and the plugin configuration is not active for the main product, then cancel.
        if ($noVariantPreselection != "active") {
            return;
        }

        // Get Meta Informations for the master product
        $metaInformation = $this->masterProductMeta->loadMetaData($event->getPage(), $masterProduct);

        // Load master product data
        $event->getPage()->setProduct($masterProduct);
        $event->getPage()->setMetaInformation($metaInformation);

        $highestUnitPrice = $this->highestUnitPrice->getHighestUnitPrice($masterProduct, $context);

        // Set Variable for Storefront
        $array = array(
            "masterProduct" => "true",
            "productId" => $masterProduct->getId(),
            "highestUnitPrice" => $highestUnitPrice
        );
        $storefrontVariables = new ArrayStruct();
        $storefrontVariables->assign($array);
        $event->getPage()->getProduct()->addExtension('EmcgnNoVariantPreselection', $storefrontVariables);
    }
}
