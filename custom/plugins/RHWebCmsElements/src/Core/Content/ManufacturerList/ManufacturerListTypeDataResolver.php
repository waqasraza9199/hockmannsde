<?php declare(strict_types=1);

namespace RHWeb\CmsElements\Core\Content\ManufacturerList;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Cms\Exception\DuplicateCriteriaKeyException;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerCollection;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;

class ManufacturerListTypeDataResolver extends AbstractCmsElementResolver
{
    public function getType(): string
    {
        return 'rhweb-manufacturer-list';
    }

    /**
     * @throws DuplicateCriteriaKeyException
     * @throws InconsistentCriteriaIdsException
     */
    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $criteria = new Criteria();
        $criteria->addSorting(new FieldSorting('name', FieldSorting::ASCENDING));
        $criteria->addAssociation('media');

        $criteriaCollection = new CriteriaCollection();
        $criteriaCollection->add('manufacturer_list_' . $slot->getUniqueIdentifier(), ProductManufacturerDefinition::class, $criteria);

        return $criteriaCollection;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $manufactuerList = new ManufacturerListStruct();

        $slot->setData($manufactuerList);

        $searchResult = $result->get('manufacturer_list_' . $slot->getUniqueIdentifier());

        if (!$searchResult || !$searchResult->getEntities() instanceof ProductManufacturerCollection) {
            return;
        }

        $manufactuerList->setProductManufacturers($searchResult->getEntities());
    }
}
