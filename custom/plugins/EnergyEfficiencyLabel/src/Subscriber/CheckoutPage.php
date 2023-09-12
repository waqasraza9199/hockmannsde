<?php declare(strict_types=1);

namespace LZYT\Enev\Subscriber;

use LZYT\Enev\Service\Product as ProductService;
use Shopware\Core\Checkout\Cart\LineItem\CartDataCollection;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\LineItem\LineItemCollection;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Storefront\Page\Checkout\Cart\CheckoutCartPageLoadedEvent;
use Shopware\Storefront\Page\Checkout\Confirm\CheckoutConfirmPageLoadedEvent;
use Shopware\Storefront\Page\Checkout\Offcanvas\OffcanvasCartPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Exception;

class CheckoutPage implements EventSubscriberInterface
{
    /** @var ProductService */
    private $productService;

    /**
     * Product constructor.
     *
     * @param ProductService $productService
     */
    public function __construct(
        ProductService $productService
    ) {
        $this->productService = $productService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CheckoutCartPageLoadedEvent::class => 'onCheckoutCartPageLoadedEvent',
            CheckoutConfirmPageLoadedEvent::class => 'onCheckoutConfirmPageLoadedEvent',
            OffcanvasCartPageLoadedEvent::class => 'onOffcanvasCartPageLoadedEvent',
        ];
    }

    /**
     * @param OffcanvasCartPageLoadedEvent $event
     * @throws Exception
     */
    public function onOffcanvasCartPageLoadedEvent(OffcanvasCartPageLoadedEvent $event): void
    {
        $context = $event->getContext();
        $products = $event->getPage()->getCart()->getData();
        $lineItems = $event->getPage()->getCart()->getLineItems();

        $this->addExtension($lineItems, $products, $context);
    }

    /**
     * @param CheckoutConfirmPageLoadedEvent $event
     * @throws Exception
     */
    public function onCheckoutConfirmPageLoadedEvent(CheckoutConfirmPageLoadedEvent $event): void
    {
        $context = $event->getContext();
        $products = $event->getPage()->getCart()->getData();
        $lineItems = $event->getPage()->getCart()->getLineItems();

        $this->addExtension($lineItems, $products, $context);
    }

    /**
     * @param CheckoutCartPageLoadedEvent $event
     * @throws Exception
     */
    public function onCheckoutCartPageLoadedEvent(CheckoutCartPageLoadedEvent $event): void
    {
        $context = $event->getContext();
        $products = $event->getPage()->getCart()->getData();
        $lineItems = $event->getPage()->getCart()->getLineItems();

        $this->addExtension($lineItems, $products, $context);
    }

    /**
     * helper to add extension
     *
     * @param LineItemCollection $lineItems
     * @param CartDataCollection $products
     * @param Context $context
     * @throws Exception
     */
    protected function addExtension(
        LineItemCollection $lineItems,
        CartDataCollection $products,
        Context $context
    ): void {
        /** @var LineItem $lineItem */
        foreach ($lineItems as $lineItem) {
            $parentId = $this->getProductParentId($lineItem, $products);
            $lineItem->addExtension('lzytEnev', $this->findEnevByCartItem($lineItem, $context, $parentId));
        }
    }

    /**
     * helper to find enev
     *
     * @param LineItem $lineItem
     * @param Context $context
     * @param string|null $parentId
     * @return EntitySearchResult|null
     * @throws Exception
     */
    private function findEnevByCartItem(LineItem $lineItem, Context $context, $parentId = null): ?EntitySearchResult
    {
        if($lineItem->getType() !== LineItem::PRODUCT_LINE_ITEM_TYPE){
            return null;
        }

        $result = null;
        try {
            $result = $this->productService->findEnevByProductId($lineItem->getId(), $context);
            if (!$result->getTotal() && $parentId) {
                $result = $this->productService->findEnevByProductId($parentId, $context);
            }
            $result = $result->getTotal() ? $result : null;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        return $result;
    }

    /**
     * helper to find parentId from CartDataCollection
     *
     * @param LineItem $lineItem
     * @param CartDataCollection $products
     * @return string|null
     */
    private function getProductParentId(LineItem $lineItem, CartDataCollection $products)
    {
        if($lineItem->getType() !== LineItem::PRODUCT_LINE_ITEM_TYPE){
            return null;
        }

        $keyName = 'product-' . $lineItem->getId();
        /** @var  ProductEntity $product */
        $product = $products->get($keyName);

        return $product !== null ? $product->getParentId() : null;
    }
}