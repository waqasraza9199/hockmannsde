<?php declare(strict_types=1);

namespace RHWeb\CmsElements\Content;

use Shopware\Core\Content\Media\Cms\ImageCmsElementResolver;
use Shopware\Core\Content\Media\Cms\ProductSliderCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\SalesChannel\Struct\ProductSliderStruct;
use Shopware\Core\Content\Cms\SalesChannel\Struct\ImageStruct;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\FieldConfig;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\EntityResolverContext;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

class RHWebShopTheLookTypeDataResolver extends AbstractCmsElementResolver
{

    public function getType(): string
    {
        return 'rhweb-shop-the-look';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $config = $slot->getFieldConfig();
        $mediaConfig = $config->get('media');

        if (!$mediaConfig || $mediaConfig->isMapped() || $mediaConfig->getValue() === null) {
            return null;
        }

        $criteria = new Criteria([$mediaConfig->getValue()]);

        $criteriaCollection = new CriteriaCollection();
        $criteriaCollection->add('media_' . $slot->getUniqueIdentifier(), MediaDefinition::class, $criteria);

        if (!$products = $config->get('products')) {
            return null;
        }

        $criteria = new Criteria($products->getValue());
        $criteria->addAssociation('cover');
        $criteriaCollection->add('products_' . $slot->getUniqueIdentifier(), ProductDefinition::class, $criteria);

        return $criteriaCollection;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $config = $slot->getFieldConfig();

        $image = new ImageStruct();
        $slider = new ProductSliderStruct();
        $struct = new RHWebShopTheLookStruct();

        $struct->setMedia($image);
        $struct->setProducts($slider);

        $slot->setData($struct);

        $mediaConfig = $config->get('media');
        if ($mediaConfig && $mediaConfig->getValue()) {
            $this->addMediaEntity($slot, $image, $result, $mediaConfig, $resolverContext);
        }

        if (!$productConfig = $config->get('products')) {
            return;
        }

        $this->enrichFromSearch($slider, $result, 'products_' . $slot->getUniqueIdentifier());
    }

    private function addMediaEntity(CmsSlotEntity $slot, ImageStruct $image, ElementDataCollection $result, FieldConfig $config, ResolverContext $resolverContext): void
    {
        if ($config->isMapped() && $resolverContext instanceof EntityResolverContext) {
            /** @var MediaEntity|null $media */
            $media = $this->resolveEntityValue($resolverContext->getEntity(), $config->getValue());

            if ($media !== null) {
                $image->setMediaId($media->getUniqueIdentifier());
                $image->setMedia($media);
            }
        }

        if ($config->isStatic()) {
            $image->setMediaId($config->getValue());

            $searchResult = $result->get('media_' . $slot->getUniqueIdentifier());
            if (!$searchResult) {
                return;
            }

            /** @var MediaEntity|null $media */
            $media = $searchResult->get($config->getValue());
            if (!$media) {
                return;
            }

            $image->setMedia($media);
        }
    }

    private function enrichFromSearch(ProductSliderStruct $slider, ElementDataCollection $result, string $searchKey): void
    {
        $searchResult = $result->get($searchKey);
        if (!$searchResult) {
            return;
        }

        /** @var ProductCollection|null $products */
        $products = $searchResult->getEntities();
        if (!$products) {
            return;
        }

        $slider->setProducts($products);
    }

}
