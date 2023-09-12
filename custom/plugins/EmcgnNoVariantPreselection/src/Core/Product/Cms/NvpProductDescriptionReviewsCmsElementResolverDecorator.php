<?php declare(strict_types=1);

namespace EmcgnNoVariantPreselection\Core\Product\Cms;

use EmcgnNoVariantPreselection\Component\MasterProductReviews;
use EmcgnNoVariantPreselection\Core\Product\Cms\Helper\ResolverHelperService;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\CmsElementResolverInterface;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Cms\SalesChannel\Struct\ProductDescriptionReviewsStruct;

class NvpProductDescriptionReviewsCmsElementResolverDecorator implements CmsElementResolverInterface
{
    private CmsElementResolverInterface $productDescriptionReviewsCmsElementResolver;
    private MasterProductReviews $masterProductReviews;
    private ResolverHelperService $resolverHelper;

    public function __construct(CmsElementResolverInterface $productDescriptionReviewsCmsElementResolver, MasterProductReviews $masterProductReviews, ResolverHelperService $resolverHelper)
    {
        $this->productDescriptionReviewsCmsElementResolver = $productDescriptionReviewsCmsElementResolver;
        $this->masterProductReviews = $masterProductReviews;
        $this->resolverHelper = $resolverHelper;
    }

    public function getType(): string
    {
        return $this->productDescriptionReviewsCmsElementResolver->getType();
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        return $this->productDescriptionReviewsCmsElementResolver->collect($slot, $resolverContext);
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        // Use ResolverHelper to read out the master product data and check the config
        $masterProduct = $this->resolverHelper->resolverHelper($resolverContext);

        if ($masterProduct == ""){
            $this->productDescriptionReviewsCmsElementResolver->enrich($slot, $resolverContext, $result);

            return;
        }

        // Load element data
        $this->productDescriptionReviewsCmsElementResolver->enrich($slot, $resolverContext, $result);

        // Replace product data with master product data
        $data = new ProductDescriptionReviewsStruct();
        $slot->setData($data);

        if ($masterProduct !== null) {
            $data->setProduct($masterProduct);
            $data->setReviews($this->masterProductReviews->loadProductReviews($masterProduct, $resolverContext->getRequest(), $resolverContext->getSalesChannelContext()));
        }
    }
}
