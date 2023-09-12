<?php declare(strict_types=1);

namespace RHWeb\CmsElements\Content;

use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerCollection;
use Shopware\Core\Framework\Struct\Struct;

class RHWebBrandSliderStruct extends Struct
{
    /**
     * @var ProductCollection|null
     */
    protected $brands;

    public function getBrands(): ?ProductManufacturerCollection
    {
        return $this->brands;
    }

    public function setBrands(ProductManufacturerCollection $brands): void
    {
        $this->brands = $brands;
    }
}
