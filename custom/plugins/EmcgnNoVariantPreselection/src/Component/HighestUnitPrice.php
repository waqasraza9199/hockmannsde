<?php

namespace EmcgnNoVariantPreselection\Component;

use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\MaxAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\AggregationResult\Metric\MaxResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\DependencyInjection\Container;

class HighestUnitPrice
{
    private Container $container;
    private $salesChannelRepository;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->salesChannelRepository = $this->container->get('sales_channel.product.repository');
    }

    /**
     * Search for the highest unit price in all variants.
     *
     * @param SalesChannelProductEntity $masterProduct
     * @param SalesChannelContext $context
     * @return float|int|string|null
     */
    public function getHighestUnitPrice(SalesChannelProductEntity $masterProduct, SalesChannelContext $context)
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter("parentId", $masterProduct->getId()));
        $criteria->addAggregation(new MaxAggregation('max-price', 'price'));
        $criteria->setLimit(1);

        $result = $this->salesChannelRepository->search($criteria, $context);

        /** @var MaxResult $aggregation */
        $aggregation = $result->getAggregations()->get('max-price');

        return $aggregation->getMax();
    }
}
