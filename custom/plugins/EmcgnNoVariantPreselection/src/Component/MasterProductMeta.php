<?php

namespace EmcgnNoVariantPreselection\Component;

use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Storefront\Page\MetaInformation;
use Shopware\Storefront\Page\Product\ProductPage;

class MasterProductMeta
{
    /**
     * Set Meta Informations for the master product
     *
     * @param ProductPage $page
     * @param SalesChannelProductEntity $masterProduct
     * @return MetaInformation
     */
    public function loadMetaData(ProductPage $page, SalesChannelProductEntity $masterProduct): MetaInformation
    {
        $metaInformation = $page->getMetaInformation();

        $metaDescription = $masterProduct->getTranslation('metaDescription') ?? $masterProduct->getTranslation('description');
        $metaInformation->setMetaDescription((string) $metaDescription);

        $metaInformation->setMetaKeywords((string) $masterProduct->getTranslation('keywords'));

        $metaTitle = $masterProduct->getTranslation('metaTitle') ?? $masterProduct->getTranslation('name') . ' | ' . $masterProduct->getProductNumber();
        $metaInformation->setMetaTitle((string) $metaTitle);

        return $metaInformation;
    }
}
