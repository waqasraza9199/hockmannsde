<?php

declare(strict_types=1);

namespace WebLa_IntelligentCrossSelling\Core\Content\CrossSellPropertyGroup;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;

use Shopware\Core\Content\Property\PropertyGroupDefinition;

use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class CrossSellPropertyGroupDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'webla_intelligent_cross_selling_property_group';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return CrossSellPropertyGroupEntity::class;
    }

    public function getCollectionClass(): string
    {
        return CrossSellPropertyGroupCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new FkField('property_group_id', 'propertyGroupId', PropertyGroupDefinition::class))->addFlags(new Required()),
            new IntField('weight', 'weight'),
            new CreatedAtField(),
            new UpdatedAtField(),
            new OneToOneAssociationField('property_group', 'property_group_id', 'id', PropertyGroupDefinition::class, true)

        ]);
    }
}
