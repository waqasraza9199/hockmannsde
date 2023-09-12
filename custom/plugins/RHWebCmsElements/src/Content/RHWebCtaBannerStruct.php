<?php declare(strict_types=1);

namespace RHWeb\CmsElements\Content;

use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Framework\Struct\Struct;
use Shopware\Core\Content\Cms\SalesChannel\Struct\ImageStruct;

class RHWebCtaBannerStruct extends ImageStruct
{
    protected $category;
    public function getCategory(): ?CategoryEntity
    {
        return $this->category;
    }
    public function setCategory(CategoryEntity $category): void
    {
        $this->category = $category;
    }
}
