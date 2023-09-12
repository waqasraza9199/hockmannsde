<?php declare(strict_types=1);

namespace WebLa_IntelligentCrossSelling\Core\Content\CrossSellPropertyGroup;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

class CrossSellPropertyGroupCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return CrossSellPropertyGroupEntity::class;
    }
}
