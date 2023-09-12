<?php declare(strict_types=1);

namespace LZYT\Enev\Core\Content\Enev;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void              add(EnevEntity $entity)
 * @method void              set(string $key, EnevEntity $entity)
 * @method EnevEntity[]    getIterator()
 * @method EnevEntity[]    getElements()
 * @method EnevEntity|null get(string $key)
 * @method EnevEntity|null first()
 * @method EnevEntity|null last()
 */
class EnevCollection extends EntityCollection
{
    /**
     * return entity class name
     *
     * @return string
     */
    protected function getExpectedClass(): string
    {
        return EnevEntity::class;
    }
}
