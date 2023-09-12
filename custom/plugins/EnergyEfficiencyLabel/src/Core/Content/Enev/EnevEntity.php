<?php declare(strict_types=1);

namespace LZYT\Enev\Core\Content\Enev;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class EnevEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $productId;

    /**
     * @var bool
     */
    protected $active;

    /**
     * @var int
     */
    protected $position;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var string|null
     */
    protected $spectrumFrom;

    /**
     * @var string|null
     */
    protected $spectrumTo;

    /**
     * @var string
     */
    protected $color;

    /**
     * @var string|null
     */
    protected $mediaId;

    /**
     * @var string|null
     */
    protected $datasheetId;

    /**
     * @var string|null
     */
    protected $iconId;

    /**
     * @return string
     */
    public function getProductId(): string
    {
        return $this->productId;
    }

    /**
     * @param string $productId
     */
    public function setProductId(string $productId): void
    {
        $this->productId = $productId;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @param string $class
     */
    public function setClass(string $class): void
    {
        $this->class = $class;
    }

    /**
     * @return string|null
     */
    public function getSpectrumFrom(): ?string
    {
        return $this->spectrumFrom;
    }

    /**
     * @param string|null $spectrum_from
     */
    public function setSpectrumFrom(?string $spectrum_from): void
    {
        $this->spectrumFrom = $spectrum_from;
    }

    /**
     * @return string|null
     */
    public function getSpectrumTo(): ?string
    {
        return $this->spectrumTo;
    }

    /**
     * @param string|null $spectrum_to
     */
    public function setSpectrumTo(?string $spectrum_to): void
    {
        $this->spectrumTo = $spectrum_to;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return strtolower($this->color);
    }

    /**
     * @param string $color
     */
    public function setColor(string $color): void
    {
        $this->color = strtolower($color);
    }

    /**
     * @return string|null
     */
    public function getMediaId(): ?string
    {
        return $this->mediaId;
    }

    /**
     * @param string|null $mediaId
     */
    public function setMediaId(?string $mediaId): void
    {
        $this->mediaId = $mediaId;
    }

    /**
     * @return string|null
     */
    public function getDatasheetId(): ?string
    {
        return $this->datasheetId;
    }

    /**
     * @param string|null $datasheetId
     */
    public function setDatasheetId(?string $datasheetId): void
    {
        $this->datasheetId = $datasheetId;
    }

    /**
     * @return string|null
     */
    public function getIconId(): ?string
    {
        return $this->iconId;
    }

    /**
     * @param string|null $iconId
     */
    public function setIconId(?string $iconId): void
    {
        $this->iconId = $iconId;
    }
}
