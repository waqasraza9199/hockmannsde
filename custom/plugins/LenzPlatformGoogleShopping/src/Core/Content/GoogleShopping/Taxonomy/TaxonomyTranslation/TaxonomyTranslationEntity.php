<?php declare(strict_types = 1);

namespace Lenz\GoogleShopping\Core\Content\GoogleShopping\Taxonomy\TaxonomyTranslation;
use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;

class TaxonomyTranslationEntity extends TranslationEntity
{
    /**
     * @var string
     */
    protected $name;

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
