<?php declare(strict_types=1);

namespace Lenz\GoogleShopping\Core\Content\GoogleShopping\Taxonomy;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                         add(TaxonomyEntity $entity)
 * @method void                         set(string $key, TaxonomyEntity $entity)
 * @method TaxonomyEntity[]    getIterator()
 * @method TaxonomyEntity[]    getElements()
 * @method TaxonomyEntity|null get(string $key)
 * @method TaxonomyEntity|null first()
 * @method TaxonomyEntity|null last()
 */
class TaxonomyCollection extends EntityCollection {

    protected function getExpectedClass(): string
    {
        return TaxonomyEntity::class;
    }
}
