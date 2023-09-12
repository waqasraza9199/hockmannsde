<?php declare(strict_types=1);

namespace EmcgnNoVariantPreselection\Core\Product\Cms;

use EmcgnNoVariantPreselection\Core\Product\Cms\Helper\ResolverHelperService;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\CmsElementResolverInterface;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Cms\SalesChannel\Struct\ImageSliderItemStruct;
use Shopware\Core\Content\Cms\SalesChannel\Struct\ImageSliderStruct;
use Shopware\Core\Content\Product\Aggregate\ProductMedia\ProductMediaCollection;
use Shopware\Core\Content\Product\Aggregate\ProductMedia\ProductMediaEntity;

class NvpImageGalleryTypeDataResolverDecorator extends AbstractCmsElementResolver
{
    private CmsElementResolverInterface $imageGalleryTypeDataResolver;
    private ResolverHelperService $resolverHelper;

    public function __construct(CmsElementResolverInterface $imageGalleryTypeDataResolver, ResolverHelperService $resolverHelper)
    {
        $this->imageGalleryTypeDataResolver = $imageGalleryTypeDataResolver;
        $this->resolverHelper = $resolverHelper;
    }

    public function getType(): string
    {
        return $this->imageGalleryTypeDataResolver->getType();
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        return $this->imageGalleryTypeDataResolver->collect($slot, $resolverContext);
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        // Use ResolverHelper to read out the master product data and check the config
        $masterProduct = $this->resolverHelper->resolverHelper($resolverContext);

        if ($masterProduct == ""){
            $this->imageGalleryTypeDataResolver->enrich($slot, $resolverContext, $result);

            return;
        }

        // Load element data
        $this->imageGalleryTypeDataResolver->enrich($slot, $resolverContext, $result);
        $sliderItemsConfig = $slot->getFieldConfig()->get('sliderItems');

        // Replace product media with master product media
        $imageSlider = new ImageSliderStruct();
        $slot->setData($imageSlider);

        // Method getStringValue exist since 6.4.2
        if (method_exists($sliderItemsConfig, 'getStringValue')) {
            $sliderItems = $this->resolveEntityValue($masterProduct, $sliderItemsConfig->getStringValue());
        } else {
            $sliderItems = $this->resolveEntityValue($masterProduct, $sliderItemsConfig->getValue());
        }

        if ($sliderItems === null || \count($sliderItems) < 1) {
            return;
        }

        // Method getStringValue exist since 6.4.6
        if (method_exists($this->imageGalleryTypeDataResolver, 'sortItemsByPosition')) {
            $this->sortItemsByPosition($sliderItems);
        }

        foreach ($sliderItems->getMedia() as $media) {
            $imageSliderItem = new ImageSliderItemStruct();
            $imageSliderItem->setMedia($media);
            $imageSlider->addSliderItem($imageSliderItem);
        }
    }

    protected function sortItemsByPosition(ProductMediaCollection $sliderItems): void
    {
        if (!$sliderItems->first() || !$sliderItems->first()->has('position')) {
            return;
        }

        $sliderItems->sort(static function (ProductMediaEntity $a, ProductMediaEntity $b) {
            return $a->get('position') - $b->get('position');
        });
    }
}
