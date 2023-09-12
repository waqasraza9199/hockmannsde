<?php declare(strict_types=1);

namespace Lenz\GoogleShopping\Core\Content\GoogleShopping\Taxonomy;

use Lenz\GoogleShopping\Core\Content\GoogleShopping\Taxonomy\TaxonomyTranslation\TaxonomyTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class TaxonomyDefinition extends EntityDefinition {

    const ENTITY_NAME = 'lenz_google_shopping_taxonomy';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return TaxonomyEntity::class;
    }

    public function getCollectionClass(): string
    {
        return TaxonomyCollection::class;
    }

    public function defineFields(): FieldCollection
    {
        $fields = [];

        // Fields.
        $fields[] = (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey());
        $fields[] = (new IntField('cat_id', 'catId'))->addFlags(new Required());

        // Translated fields.
        $fields[] = (new TranslatedField('name'));
        $fields[] = new TranslationsAssociationField(TaxonomyTranslationDefinition::class, 'lenz_google_shopping_taxonomy_id');

        return new FieldCollection($fields);
    }

    public function getDefaults(): array
    {
        return [

        ];
    }
}
