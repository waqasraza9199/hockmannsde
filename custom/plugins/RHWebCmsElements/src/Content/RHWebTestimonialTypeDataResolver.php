<?php declare(strict_types=1);

namespace RHWeb\CmsElements\Content;

use Shopware\Core\Content\Media\Cms\ImageCmsElementResolver;

class RHWebTestimonialTypeDataResolver extends ImageCmsElementResolver
{
    public function getType(): string
    {
        return 'rhweb-testimonial';
    }
}
