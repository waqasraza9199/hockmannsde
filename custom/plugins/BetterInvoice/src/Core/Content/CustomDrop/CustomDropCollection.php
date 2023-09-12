<?php declare(strict_types=1);

namespace LZYT8\BetterInvoice\Core\Content\CustomDrop;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

class CustomDropCollection extends EntityCollection
{
    /**
     * {@inheritDoc}
     */
    protected function getExpectedClass(): string
    {
        return CustomDropEntity::class;
    }
}