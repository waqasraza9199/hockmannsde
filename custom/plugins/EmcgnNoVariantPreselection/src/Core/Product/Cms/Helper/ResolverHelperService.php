<?php declare(strict_types=1);

namespace EmcgnNoVariantPreselection\Core\Product\Cms\Helper;

use EmcgnNoVariantPreselection\Component\CheckConfig;
use EmcgnNoVariantPreselection\Component\CheckId;
use EmcgnNoVariantPreselection\Component\MasterProduct;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;

class ResolverHelperService
{
    private MasterProduct $masterProduct;
    private CheckId $checkId;
    private CheckConfig $checkConfig;

    public function __construct(MasterProduct $masterProduct, CheckId $checkId, CheckConfig $checkConfig)
    {
        $this->masterProduct = $masterProduct;
        $this->checkId = $checkId;
        $this->checkConfig = $checkConfig;
    }

    /**
     * Universal resolverHelper to read out the master prodauct data and check the config for all element resolver types
     *
     * @param $resolverContext
     * @return SalesChannelProductEntity|string
     */
    public function resolverHelper($resolverContext)
    {
        // Read out product data
        $product = $resolverContext->getEntity();

        if ($product === null) {
            return "";
        }

        // If no product entity, then execute original resolver
        if (!method_exists($product, 'getParentId')){
            return "";
        }

        // If no variant article, then execute original resolver
        $parentId = $product->getParentId();
        if ($parentId === null) {
            return "";
        }

        $context = $resolverContext->getSalesChannelContext();
        $request = $resolverContext->getRequest();

        $masterProduct = $this->masterProduct->getMasterProduct($product, $context);
        $masterProduct = $this->masterProduct->getSeoCategory($masterProduct, $context);

        $comparedId = $this->checkId->compareId($request, $masterProduct);

        // If the compared Id is not the same, then execute original resolver
        if ($comparedId == false) {
            return "";
        }

        $noVariantPreselection = $this->checkConfig->getConfig($masterProduct);

        // If the plugin configuration is not set to global or the custom field is not active for the main product, then execute original resolver
        if ($noVariantPreselection != "active") {
            return "";
        }

        return $masterProduct;
    }
}
