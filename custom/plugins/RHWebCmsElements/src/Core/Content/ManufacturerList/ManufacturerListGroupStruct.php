<?php declare(strict_types=1);

namespace RHWeb\CmsElements\Core\Content\ManufacturerList;

use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerCollection;
use Shopware\Core\Framework\Struct\Struct;

class ManufacturerListGroupStruct extends Struct
{
    /**
     * @var ProductManufacturerCollection|null
     */
    protected $productManufacturers;

    /**
     * @var string|null
     */
    protected $title;

    /**
     * @return ProductManufacturerCollection|null
     */
    public function getProductManufacturers(): ?ProductManufacturerCollection
    {
        return $this->productManufacturers;
    }

    /**
     * @param ProductManufacturerCollection|null $productManufacturers
     */
    public function setProductManufacturers(?ProductManufacturerCollection $productManufacturers): void
    {
        $this->productManufacturers = $productManufacturers;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getApiAlias(): string
    {
        return 'cms_manufacturer_list_group';
    }
}
