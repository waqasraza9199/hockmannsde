<?php

declare(strict_types=1);

namespace WebLa_IntelligentCrossSelling\Core\Content\CrossSellSettings;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class CrossSellSettingsDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'webla_intelligent_cross_selling_settings';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return CrossSellSettingsEntity::class;
    }

    public function getCollectionClass(): string
    {
        return CrossSellSettingsCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new StringField('title', "title"))->addFlags(new Required()),
            new IntField('max_products', 'maxProducts'),
            new BoolField('active', 'active'),
            new BoolField('only_category', 'onlyCategory'),
            new BoolField('show_title', 'showTitle'),
            new CreatedAtField(),
            new UpdatedAtField()
        ]);
    }
}
