<?php declare(strict_types=1);

namespace Lenz\GoogleShopping\Core\Content\GoogleShopping\Taxonomy;

use Shopware\Core\Framework\DataAbstractionLayer\{Entity, EntityIdTrait};

class TaxonomyEntity extends Entity {

    use EntityIdTrait;

    /**
     * @var int
     */
    protected $catId;

    /**
     * @var string
     */
    protected $name;

    /**
     * @return int
     */
    public function getCatId(): int
    {
        return $this->catId;
    }

    /**
     * @param int $catId
     */
    public function setCatId(int $catId): void
    {
        $this->catId = $catId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
