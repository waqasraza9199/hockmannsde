<?php declare(strict_types = 1);

namespace Lenz\GoogleShopping\Core\Content\GoogleShopping\Taxonomy\TaxonomyTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
* @method void                         add(TaxonomyTranslationEntity $entity)
* @method void                         set(string $key, TaxonomyTranslationEntity $entity)
* @method TaxonomyTranslationEntity[]    getIterator()
* @method TaxonomyTranslationEntity[]    getElements()
* @method TaxonomyTranslationEntity|null get(string $key)
* @method TaxonomyTranslationEntity|null first()
* @method TaxonomyTranslationEntity|null last()
*/
class TaxonomyTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return TaxonomyTranslationEntity::class;
    }
}

