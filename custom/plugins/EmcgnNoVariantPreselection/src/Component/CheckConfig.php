<?php

namespace EmcgnNoVariantPreselection\Component;

use Shopware\Core\System\SystemConfig\SystemConfigService;

class CheckConfig
{
    private SystemConfigService $systemConfigService;

    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->systemConfigService = $systemConfigService;
    }

    /**
     * First check if the plugin configuration is set to global.
     * If not, then also check whether the custom field in the master product.
     *
     * @param $masterProduct
     * @return string
     */
    public function getConfig($masterProduct): string
    {
        $pluginConfig = $this->getPluginConfig();

        if ($pluginConfig == "globalMode"){
            return "active";
        }

        $masterProductConfig = $this->getMasterProductConfig($masterProduct);

        if (array_key_exists('emcgn_no_variant_preselection_active', $masterProductConfig) && $masterProductConfig['emcgn_no_variant_preselection_active'] == "true"){
            return "active";
        }

        return "";
    }

    /**
     * Read out plugin configuration
     *
     * @return array|bool|float|int|string|null
     */
    private function getPluginConfig()
    {
        return $this->systemConfigService->get('EmcgnNoVariantPreselection.config.pluginMode');
    }

    /**
     * Read out custom field setting
     *
     * @param $masterProduct
     * @return mixed
     */
    private function getMasterProductConfig($masterProduct)
    {
        return $masterProduct->getCustomFields();
    }
}
