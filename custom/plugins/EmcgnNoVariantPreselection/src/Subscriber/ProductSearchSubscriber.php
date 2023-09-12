<?php

namespace EmcgnNoVariantPreselection\Subscriber;

use Shopware\Core\Content\Product\Events\ProductSearchResultEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductSearchSubscriber implements EventSubscriberInterface
{
    private ProductListingSubscriber $productListingSubscriber;

    public static function getSubscribedEvents()
    {
        return [
            ProductSearchResultEvent::class => ['onProductSearch', 50]
        ];
    }

    public function __construct(ProductListingSubscriber $productListingSubscriber)
    {
        $this->productListingSubscriber = $productListingSubscriber;
    }

    /**
     * Loading search page
     *
     * @param ProductSearchResultEvent $event
     * @return void
     */
    public function onProductSearch(ProductSearchResultEvent $event)
    {
        $this->productListingSubscriber->onProductListing($event);
    }
}
