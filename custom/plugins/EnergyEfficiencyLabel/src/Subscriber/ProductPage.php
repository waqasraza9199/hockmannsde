<?php declare(strict_types=1);

namespace LZYT\Enev\Subscriber;

use LZYT\Enev\Core\Content\Enev\EnevDefinition;
use LZYT\Enev\Service\Filter;
use LZYT\Enev\Service\Product as ProductService;
use Shopware\Core\Content\Product\Events\ProductListingResultEvent;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelEntityLoadedEvent;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Exception;

class ProductPage implements EventSubscriberInterface
{
    /** @var ProductService */
    private $productService;

    /** @var Filter */
    private $filterService;

    /**
     * Product constructor.
     *
     * @param ProductService $productService
     * @param Filter $filterService
     */
    public function __construct(
        ProductService $productService,
        Filter $filterService
    ) {
        $this->productService = $productService;
        $this->filterService = $filterService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductPageLoadedEvent::class => 'onProductPageLoadedEvent',
            ProductListingResultEvent::class => 'onProductListingResultEvent',
            'sales_channel.product.loaded' => 'onSalesChannelEntityLoadedEvent',
        ];
    }

    /**
     * add data into listing
     *
     * @param ProductListingResultEvent $event
     * @throws Exception
     */
    public function onProductListingResultEvent(ProductListingResultEvent $event): void
    {
        $salesChannelContext = $event->getSalesChannelContext();
        $result = $event->getResult();

        /** workaround to set the correct filter list */
        $this->filterService->setFilterList($result, EnevDefinition::ENTITY_NAME, 'class');

        /** @var SalesChannelProductEntity $product */
        foreach ($result as $product) {
            $product->addExtension('lzytEnev', $this->findEnevByProduct($product, $salesChannelContext));
        }
    }

    /**
     * add data into detail
     *
     * @param ProductPageLoadedEvent $event
     * @throws Exception
     */
    public function onProductPageLoadedEvent(ProductPageLoadedEvent $event): void
    {
        $product = $event->getPage()->getProduct();
        $salesChannelContext = $event->getSalesChannelContext();
        $product->addExtension('lzytEnev', $this->findEnevByProduct($product, $salesChannelContext));
    }

    /**
     * add data into product-slider
     *
     * @param SalesChannelEntityLoadedEvent $event
     * @throws Exception
     */
    public function onSalesChannelEntityLoadedEvent(SalesChannelEntityLoadedEvent $event): void
    {

        $salesChannelContext = $event->getSalesChannelContext();
        $result = $event->getEntities();

        /** @var SalesChannelProductEntity $product */
        foreach ($result as $product) {
            $product->addExtension('lzytEnev', $this->findEnevByProduct($product, $salesChannelContext));
        }
    }

    /**
     * helper to find enev
     *
     * @param ProductEntity $product
     * @param SalesChannelContext $salesChannelContext
     * @return EntitySearchResult|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function findEnevByProduct(ProductEntity $product, SalesChannelContext $salesChannelContext): ?EntitySearchResult
    {
        $result = null;
        try {
            $result = $this->productService->findEnevByProductId($product->getId(), $salesChannelContext->getContext());
            if (!$result->getTotal() && $product->getParentId()) {
                $result = $this->productService->findEnevByProductId($product->getParentId(), $salesChannelContext->getContext());
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        return $result;
    }
}