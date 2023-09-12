<?php declare(strict_types=1);

namespace TcinnThemeWareElectric\Core\Content\Media\Cms\Type;

use Shopware\Core\Content\Media\Cms\ImageCmsElementResolver;

class TcinnImageBannerTypeDataResolver extends ImageCmsElementResolver
{
    public function getType(): string
    {
        return 'twt-image-banner';

    }
}