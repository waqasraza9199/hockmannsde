<?php declare(strict_types=1);

namespace RHWeb\CmsElements\Content;

use Shopware\Core\Content\Product\Cms\ProductBoxCmsElementResolver;

class RHWebDealCounterTypeDataResolver extends ProductBoxCmsElementResolver
{

    public function getType(): string
    {
        return 'rhweb-deal-counter';
    }

}
