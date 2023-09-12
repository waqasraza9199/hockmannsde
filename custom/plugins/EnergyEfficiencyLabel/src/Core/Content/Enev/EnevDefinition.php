<?php declare(strict_types=1);

namespace LZYT\Enev\Core\Content\Enev;

use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\SearchRanking;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\VersionField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class EnevDefinition extends EntityDefinition {

    public const ENTITY_NAME = 'lzyt_enev';

    /**
     * return entity name
     *
     * @return string
     */
    public function getEntityName(): string {
        return self::ENTITY_NAME;
    }

    /**
     * helper to normalize entity name to camelCase and lcfirst
     *
     * @return string
     */
    public static function getEntityNameCamelCaseLcfirst(): string
    {
        return lcfirst(
            str_replace(
                '_',
                '',
                ucwords(static::ENTITY_NAME, '_')
            )
        );
    }

    /**
     * return collection class name
     *
     * @return string
     */
    public function getCollectionClass(): string {
        return EnevCollection::class;
    }

    /**
     * return entity class name
     * @return string
     */
    public function getEntityClass(): string {
        return EnevEntity::class;
    }

    /**
     * return fiels collection
     *
     * @return FieldCollection
     */
    protected function defineFields(): FieldCollection {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            new VersionField(),
            (new FkField('product_id', 'productId', ProductDefinition::class))->addFlags(new Required()),
            new BoolField('active', 'active'),
            new IntField('position', 'position'),
            (new StringField('class', 'class'))->addFlags(new SearchRanking(SearchRanking::ASSOCIATION_SEARCH_RANKING)),
            new StringField('spectrum_from', 'spectrumFrom'),
            new StringField('spectrum_to', 'spectrumTo'),
            new StringField('color', 'color'),
            new FkField('media_id', 'mediaId', MediaDefinition::class),
            new FkField('datasheet_id', 'datasheetId', MediaDefinition::class),
            new FkField('icon_id', 'iconId', MediaDefinition::class),
            new CreatedAtField(),
            new UpdatedAtField(),

            (new ManyToOneAssociationField('product', 'product_id', ProductDefinition::class, 'id', true))->addFlags(new SearchRanking(SearchRanking::ASSOCIATION_SEARCH_RANKING)),
            new ManyToOneAssociationField('media', 'media_id', MediaDefinition::class, 'id', false),
            new ManyToOneAssociationField('datasheet', 'datasheet_id', MediaDefinition::class, 'id', false),
            new ManyToOneAssociationField('icon', 'icon_id', MediaDefinition::class, 'id', false)
        ]);
    }
}
