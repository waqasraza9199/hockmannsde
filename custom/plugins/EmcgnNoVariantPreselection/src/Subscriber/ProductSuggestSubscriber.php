<?php

namespace EmcgnNoVariantPreselection\Subscriber;

use Shopware\Core\Content\Product\Events\ProductSuggestResultEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductSuggestSubscriber implements EventSubscriberInterface
{
    private ProductListingSubscriber $productListingSubscriber;

    public static function getSubscribedEvents()
    {
        return [
            ProductSuggestResultEvent::class => ['onProductSuggest', 50]
        ];
    }

    public function __construct(ProductListingSubscriber $productListingSubscriber)
    {
        $this->productListingSubscriber = $productListingSubscriber;
    }

    /**
     * Loading search suggest
     *
     * @param ProductSuggestResultEvent $event
     * @return void
     */
    public function onProductSuggest(ProductSuggestResultEvent $event)
    {
        $this->productListingSubscriber->onProductListing($event);
    }
}
