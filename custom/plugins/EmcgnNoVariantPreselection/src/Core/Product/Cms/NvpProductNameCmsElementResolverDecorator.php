<?php declare(strict_types=1);

namespace EmcgnNoVariantPreselection\Core\Product\Cms;

use EmcgnNoVariantPreselection\Core\Product\Cms\Helper\ResolverHelperService;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\CmsElementResolverInterface;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Cms\SalesChannel\Struct\TextStruct;

class NvpProductNameCmsElementResolverDecorator implements CmsElementResolverInterface
{
    private CmsElementResolverInterface $productNameCmsElementResolver;
    private ResolverHelperService $resolverHelper;

    public function __construct(CmsElementResolverInterface $productNameCmsElementResolver, ResolverHelperService $resolverHelper)
    {
        $this->productNameCmsElementResolver = $productNameCmsElementResolver;
        $this->resolverHelper = $resolverHelper;
    }

    public function getType(): string
    {
        return $this->productNameCmsElementResolver->getType();
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        return $this->productNameCmsElementResolver->collect($slot, $resolverContext);
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        // Use ResolverHelper to read out the master product data and check the config
        $masterProduct = $this->resolverHelper->resolverHelper($resolverContext);

        if ($masterProduct == ""){
            $this->productNameCmsElementResolver->enrich($slot, $resolverContext, $result);

            return;
        }

        // Load element data
        $this->productNameCmsElementResolver->enrich($slot, $resolverContext, $result);

        // Replace product name with master product name
        $productName = new TextStruct();
        $slot->setData($productName);
        $productName->setContent($masterProduct->getName());
    }
}
