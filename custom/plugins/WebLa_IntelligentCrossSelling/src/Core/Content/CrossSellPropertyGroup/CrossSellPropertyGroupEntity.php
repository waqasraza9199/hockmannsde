<?php declare(strict_types=1);

namespace WebLa_IntelligentCrossSelling\Core\Content\CrossSellPropertyGroup;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class CrossSellPropertyGroupEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $propertyGroupId;

    /**
     * @var int
     */
    protected $weight;

    public function getPropertyGroupId(): string
    {
        return $this->propertyGroupId;
    }

    public function setPropertyGroupId(string $propertyGroupId): void
    {
        $this->propertyGroupId = $propertyGroupId;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): void
    {
        $this->weight = $weight;
    }

}
