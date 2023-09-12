<?php declare(strict_types=1);

namespace WebLa_IntelligentCrossSelling\Core\Content\CrossSellSettings;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class CrossSellSettingsEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var int
     */
    protected $maxProducts;

    /**
     * @var bool
     */
    protected $active;

    /**
     * @var bool
     */
    protected $onlyCategory;

    /**
     * @var bool
     */
    protected $showTitle;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setPropertyGroupId(string $title): void
    {
        $this->title = $title;
    }

    public function setMaxProducts(int $maxProducts): void
    {
        $this->weight = $maxProducts;
    }

    public function getMaxProducts(): int
    {
        return $this->maxProducts;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function isShowTitle(): bool
    {
        return $this->showTitle;
    }

    public function setShowTitle(bool $showTitle): void
    {
        $this->showTitle = $showTitle;
    }

    public function isOnlyCategory(): ?bool
    {
        return $this->onlyCategory;
    }

    public function setOnlyCategory(bool $onlyCategory): void
    {
        $this->onlyCategory = $onlyCategory;
    }

}
