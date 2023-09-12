<?php

namespace EmcgnNoVariantPreselection\Component;

use Shopware\Core\Content\Category\Service\CategoryBreadcrumbBuilder;
use Shopware\Core\Content\Product\Aggregate\ProductMedia\ProductMediaCollection;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\Product\ProductPageCriteriaEvent;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MasterProduct
{
    private Container $container;
    private CategoryBreadcrumbBuilder $breadcrumbBuilder;
    private EventDispatcherInterface $eventDispatcher;

    private $salesChannelRepository;

    public function __construct(Container $container, CategoryBreadcrumbBuilder $breadcrumbBuilder, EventDispatcherInterface $eventDispatcher)
    {
        $this->container = $container;
        $this->breadcrumbBuilder = $breadcrumbBuilder;
        $this->eventDispatcher = $eventDispatcher;

        $this->salesChannelRepository = $this->container->get('sales_channel.product.repository');
    }

    /**
     * Read out master product data
     *
     * @param SalesChannelProductEntity $product
     * @param SalesChannelContext $context
     * @return SalesChannelProductEntity
     */
    public function getMasterProduct(SalesChannelProductEntity $product, SalesChannelContext $context): SalesChannelProductEntity
    {
        $parentId = $product->getParentId();

        $productCriteria = $this->getProductCriteria($parentId);

        $this->eventDispatcher->dispatch(new ProductPageCriteriaEvent($parentId, $productCriteria, $context));

        $masterProduct = $this->salesChannelRepository
            ->search($productCriteria, $context)
            ->first();

        // Fallback to variant if master article cannot be found in the DAL
        if ($masterProduct == null) {
            return $product;
        }

        $masterProduct = $this->sortMedia($masterProduct);

        return $masterProduct;
    }

    private function getProductCriteria(string $parentId): Criteria
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $parentId));
        $criteria->addAssociation('manufacturer');
        $criteria->addAssociation('manufacturer.media');
        $criteria->addAssociation('seoUrls');
        $criteria->addAssociation('media');
        $criteria->addAssociation('configuratorSettings');
        $criteria->addAssociation('properties.group');
        $criteria->addAssociation('options');
        $criteria->addAssociation('productReviews');

        return $criteria;
    }

    /**
     * Get the Seo Category from master product
     *
     * @param SalesChannelProductEntity $masterProduct
     * @param SalesChannelContext $context
     * @return SalesChannelProductEntity
     */
    public function getSeoCategory(SalesChannelProductEntity $masterProduct, SalesChannelContext $context): SalesChannelProductEntity
    {
        $masterProduct->setSeoCategory(
            $this->breadcrumbBuilder->getProductSeoCategory($masterProduct, $context)
        );

        return $masterProduct;
    }

    /**
     * Sort media by position
     *
     * @param SalesChannelProductEntity $masterProduct
     * @return SalesChannelProductEntity
     */
    private function sortMedia(SalesChannelProductEntity $masterProduct): SalesChannelProductEntity
    {
        $sortedMedia = new ProductMediaCollection();
        $unsortedMedia = $masterProduct->getMedia()->getElements();

        usort($unsortedMedia, function($a, $b){
            return $a->position - $b->position;
        });

        foreach($unsortedMedia as $media){
            $sortedMedia->add($media);
        }

        $masterProduct->setMedia($sortedMedia);

        return $masterProduct;
    }
}
