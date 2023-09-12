<?php declare(strict_types=1);

namespace EmcgnNoVariantPreselection\Core\Product\Cms;

use EmcgnNoVariantPreselection\Core\Product\Cms\Helper\ResolverHelperService;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\CmsElementResolverInterface;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Cms\SalesChannel\Struct\BuyBoxStruct;
use Shopware\Core\Content\Product\SalesChannel\Detail\ProductConfiguratorLoader;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\CountAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\AggregationResult\Metric\CountResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class NvpBuyBoxCmsElementResolverDecorator implements CmsElementResolverInterface
{
    private CmsElementResolverInterface $buyBoxCmsElementResolver;
    private ResolverHelperService $resolverHelper;

    private ProductConfiguratorLoader $configuratorLoader;
    private EntityRepositoryInterface $repository;

    public function __construct(CmsElementResolverInterface $buyBoxCmsElementResolver, ResolverHelperService $resolverHelper, ProductConfiguratorLoader $configuratorLoader, EntityRepositoryInterface $repository)
    {
        $this->buyBoxCmsElementResolver = $buyBoxCmsElementResolver;
        $this->resolverHelper = $resolverHelper;

        $this->configuratorLoader = $configuratorLoader;
        $this->repository = $repository;
    }

    public function getType(): string
    {
        return $this->buyBoxCmsElementResolver->getType();
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        return $this->buyBoxCmsElementResolver->collect($slot, $resolverContext);
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        // Use ResolverHelper to read out the master product data and check the config
        $masterProduct = $this->resolverHelper->resolverHelper($resolverContext);

        if ($masterProduct == ""){
            $this->buyBoxCmsElementResolver->enrich($slot, $resolverContext, $result);

            return;
        }

        // Load element data
        $this->buyBoxCmsElementResolver->enrich($slot, $resolverContext, $result);

        // Set Variable for Storefront
        $array = array(
            "masterProduct" => "true",
            "productId" => $masterProduct->getId(),
        );
        $storefrontVariables = new ArrayStruct();
        $storefrontVariables->assign($array);

        // Replace product data with master product data
        $buyBox = new BuyBoxStruct();
        $slot->setData($buyBox);
        if ($masterProduct !== null) {
            $buyBox->setProduct($masterProduct);
            $buyBox->setProductId($masterProduct->getId());
            $buyBox->setConfiguratorSettings($this->configuratorLoader->load($resolverContext->getEntity(), $resolverContext->getSalesChannelContext()));
            $buyBox->setTotalReviews($this->getReviewsCount($masterProduct, $resolverContext->getSalesChannelContext()));
            $buyBox->addExtension('EmcgnNoVariantPreselection', $storefrontVariables);
        }
    }

    private function getReviewsCount(SalesChannelProductEntity $masterProduct, SalesChannelContext $context): int
    {
        $reviewCriteria = $this->createReviewCriteria($context, $masterProduct->getId());

        $aggregation = $this->repository->aggregate($reviewCriteria, $context->getContext())->get('review-count');

        return $aggregation instanceof CountResult ? $aggregation->getCount() : 0;
    }

    private function createReviewCriteria(SalesChannelContext $context, string $productId): Criteria
    {
        $criteria = new Criteria();

        $reviewFilters[] = new EqualsFilter('status', true);
        if ($context->getCustomer() !== null) {
            $reviewFilters[] = new EqualsFilter('customerId', $context->getCustomer()->getId());
        }

        $criteria->addFilter(
            new MultiFilter(MultiFilter::CONNECTION_AND, [
                new MultiFilter(MultiFilter::CONNECTION_OR, $reviewFilters),
                new MultiFilter(MultiFilter::CONNECTION_OR, [
                    new EqualsFilter('product.id', $productId),
                    new EqualsFilter('product.parentId', $productId),
                ]),
            ])
        );

        $criteria->addAggregation(new CountAggregation('review-count', 'id'));

        return $criteria;
    }
}
