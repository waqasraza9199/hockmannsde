<?php declare(strict_types=1);

namespace Lenz\GoogleShopping\Core\Content\GoogleShopping\Taxonomy\TaxonomyTranslation;

use Lenz\GoogleShopping\Core\Content\GoogleShopping;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class TaxonomyTranslationDefinition extends EntityTranslationDefinition
{
    public function getEntityName(): string
    {
        return 'lenz_google_shopping_taxonomy_translation';
    }

    public function getCollectionClass(): string
    {
        return TaxonomyTranslationCollection::class;
    }

    public function getEntityClass(): string
    {
        return TaxonomyTranslationEntity::class;
    }

    public function getParentDefinitionClass(): string
    {
        return GoogleShopping\Taxonomy\TaxonomyDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('name', 'name'))->addFlags(new Required()),
        ]);
    }
}

