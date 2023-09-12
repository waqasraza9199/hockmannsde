<?php declare(strict_types=1);

namespace LZYT\Enev\Core\Content\Product;

use LZYT\Enev\Core\Content\Enev\EnevDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ProductExtension extends EntityExtension
{
    /**
     * @return string
     */
    public function getDefinitionClass(): string
    {
        return ProductDefinition::class;
    }

    /**
     * @param FieldCollection $collection
     */
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new OneToManyAssociationField(
                EnevDefinition::getEntityNameCamelCaseLcfirst(),
                EnevDefinition::class,
                'product_id',
                'id'
            ))
        );
    }
}
