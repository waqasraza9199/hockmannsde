<?php declare(strict_types=1);

namespace WebLa_IntelligentCrossSelling\Core\Content\CrossSellSettings;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

class CrossSellSettingsCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return CrossSellSettingsEntity::class;
    }
}
