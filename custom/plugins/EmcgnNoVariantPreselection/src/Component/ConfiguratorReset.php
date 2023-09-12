<?php

namespace EmcgnNoVariantPreselection\Component;

use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Content\Seo\SeoUrlPlaceholderHandlerInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Framework\Routing\RequestTransformer;
use Symfony\Component\HttpFoundation\Request;

class ConfiguratorReset
{
    private SeoUrlPlaceholderHandlerInterface $seoUrlPlaceholderHandler;

    public function __construct(SeoUrlPlaceholderHandlerInterface $seoUrlPlaceholderHandler) {
        $this->seoUrlPlaceholderHandler = $seoUrlPlaceholderHandler;
    }

    /**
     * Get the url to the main product for the configurator reset button.
     *
     * @param SalesChannelContext $context
     * @param SalesChannelProductEntity $masterProduct
     * @param Request $request
     * @return string
     */
    public function getResetUrl(SalesChannelContext $context, SalesChannelProductEntity $masterProduct, Request $request): string
    {
        $host = $request->attributes->get(RequestTransformer::SALES_CHANNEL_ABSOLUTE_BASE_URL)
            . $request->attributes->get(RequestTransformer::SALES_CHANNEL_BASE_URL);

        return $this->seoUrlPlaceholderHandler->replace(
            $this->seoUrlPlaceholderHandler->generate(
                'frontend.detail.page',
                ['productId' => $masterProduct->getId()]
            ),
            $host,
            $context
        );
    }
}
