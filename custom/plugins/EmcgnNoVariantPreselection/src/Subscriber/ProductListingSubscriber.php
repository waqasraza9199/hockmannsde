<?php

namespace EmcgnNoVariantPreselection\Subscriber;

use EmcgnNoVariantPreselection\Component\CheckConfig;
use EmcgnNoVariantPreselection\Component\HighestUnitPrice;
use EmcgnNoVariantPreselection\Component\MasterProduct;
use Shopware\Core\Content\Product\Events\ProductListingResultEvent;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductListingSubscriber implements EventSubscriberInterface
{
    private MasterProduct $masterProduct;
    private CheckConfig $checkConfig;
    private HighestUnitPrice $highestUnitPrice;

    public static function getSubscribedEvents(): array
    {
        return [
            ProductListingResultEvent::class => ['onProductListing', 50]
        ];
    }

    public function __construct(MasterProduct $masterProduct, CheckConfig $checkConfig, HighestUnitPrice $highestUnitPrice)
    {
        $this->masterProduct = $masterProduct;
        $this->checkConfig = $checkConfig;
        $this->highestUnitPrice = $highestUnitPrice;
    }

    /**
     * Loading listing page
     *
     * @param ProductListingResultEvent $event
     * @return void
     */
    public function onProductListing(ProductListingResultEvent $event)
    {
        $result = $event->getResult();
        $context = $event->getSalesChannelContext();

        // Read out variants from elements
        $masterProducts = array();

        foreach ($result as $key => $product) {
            $parentId = $product->getParentId();

            if ($parentId != null) {
                $masterProduct = $this->masterProduct->getMasterProduct($product, $context);

                $noVariantPreselection = $this->checkConfig->getConfig($masterProduct);

                // If the plugin configuration is set to global or the custom field is active for the main product.
                if ($noVariantPreselection == "active") {
                    $highestUnitPrice = $this->highestUnitPrice->getHighestUnitPrice($masterProduct, $context);

                    // Set Variable for Storefront
                    $array = array(
                        "masterProduct" => "true",
                        "highestUnitPrice" => $highestUnitPrice
                    );
                    $storefrontVariables = new ArrayStruct();
                    $storefrontVariables->assign($array);
                    $masterProduct->addExtension('EmcgnNoVariantPreselection', $storefrontVariables);

                    $masterProducts[$key] = $masterProduct;
                }
            }
        }

        // Replace variants with master products in elements
        $elements = $result->getElements();
        $elementsNew = array_merge($elements, $masterProducts);

        foreach ($elementsNew as $key => $masterProduct) {
            $result->set($key, $masterProduct);
        }

        // Reset entities
        $entities = $result->getEntities();

        foreach ($entities as $key => $entity) {
            $entities->remove($key);
        }

        // Refill entities with values from elements
        foreach ($elementsNew as $key => $masterProduct) {
            $entities->set($key, $masterProduct);
        }
    }
}
