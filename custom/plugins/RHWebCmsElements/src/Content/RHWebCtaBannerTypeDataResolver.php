<?php declare(strict_types=1);

namespace RHWeb\CmsElements\Content;

use RHWeb\CmsElements\Content\RHWebCtaBannerStruct;
use Shopware\Core\Content\Media\Cms\ImageCmsElementResolver;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\FieldConfig;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\EntityResolverContext;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Category\CategoryDefinition;
use Shopware\Core\Content\Category\CategoryCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

class RHWebCtaBannerTypeDataResolver extends ImageCmsElementResolver
{
    public function getType(): string
    {
        return 'rhweb-cta-banner';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {

        $criteriaCollection = parent::collect($slot, $resolverContext);

        $config = $slot->getFieldConfig();

        $categoryConfig = $config->get('category');

        if (!$categoryConfig || $categoryConfig->isMapped() || $categoryConfig->getValue() === null) {
            return $criteriaCollection;
        }

        $criteria = new Criteria([$categoryConfig->getValue()]);
        $criteria->addAssociation('media');

        if (!$criteriaCollection) {
            $criteriaCollection = new CriteriaCollection();
        }

        $criteriaCollection->add('category' . $slot->getUniqueIdentifier(), CategoryDefinition::class, $criteria);

        return $criteriaCollection;

    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {

        parent::enrich($slot, $resolverContext, $result);

        $categoryBanner = new RHWebCtaBannerStruct();
        $media = $slot->getData();
        if ($media->getMedia()) {
            $categoryBanner->setMedia($media->getMedia());
        }
        $slot->setData($categoryBanner);

        $config = $slot->getFieldConfig();
        $categoryConfig = $config->get('category');

        if (!$categoryConfig || $categoryConfig->getValue() === null) {
            return;
        }

        if ($resolverContext instanceof EntityResolverContext && $categoryConfig->isMapped()) {
            $category = $this->resolveEntityValue($resolverContext->getEntity(), $categoryConfig->getValue());
            if ($category) {
                $categoryBanner->setCategory($category);
            }
        }

        if ($categoryConfig->isStatic()) {
            $this->resolveCategoryFromRemote($slot, $categoryBanner, $result, $categoryConfig->getValue());
        }

    }

    private function resolveCategoryFromRemote(CmsSlotEntity $slot, RHWebCtaBannerStruct $categoryBanner, ElementDataCollection $result, string $categoryId): void
    {

        $searchResult = $result->get('category' . $slot->getUniqueIdentifier());

        if (!$searchResult) {
            return;
        }

        $category = $searchResult->get($categoryId);
        if (!$category) {
            return;
        }

        $categoryBanner->setCategory($category);

    }

}
