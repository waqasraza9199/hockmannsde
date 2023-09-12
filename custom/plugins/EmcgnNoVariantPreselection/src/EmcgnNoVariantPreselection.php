<?php declare(strict_types=1);

namespace EmcgnNoVariantPreselection;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Shopware\Core\System\CustomField\CustomFieldTypes;

class EmcgnNoVariantPreselection extends Plugin
{
    public function install(InstallContext $installContext): void
    {
        $this->addCustomField($installContext);

        parent::install($installContext);
    }

    public function postInstall(InstallContext $installContext): void
    {
        $this->swagCustomizedProductsChange();

        parent::postInstall($installContext);
    }

    public function postUpdate(UpdateContext $updateContext): void
    {
        $this->swagCustomizedProductsChange();

        parent::postUpdate($updateContext);
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        if ($uninstallContext->keepUserData()) {
            parent::uninstall($uninstallContext);

            return;
        }

        $this->removeCustomField($uninstallContext);

        parent::uninstall($uninstallContext);
    }

    /**
     * If SwagCustomizedProducts is installed change its "InstalledAt"-date,
     * so that this plugin can override twig-blocks of SwagCustomizedProducts
     *
     * @return void
     */
    private function swagCustomizedProductsChange()
    {
        $otherPluginName = 'SwagCustomizedProducts';
        $myPluginName = 'EmcgnNoVariantPreselection';
        $pluginRepo = $this->container->get('plugin.repository');

        // Get plugins
        $plugins = $pluginRepo->search(
            (new Criteria())->addFilter(
                new MultiFilter(
                    MultiFilter::CONNECTION_OR,
                    [
                        new ContainsFilter('name', $otherPluginName),
                        new ContainsFilter('name', $myPluginName)
                    ]
                )
            ),
            \Shopware\Core\Framework\Context::createDefaultContext()
        );

        // Assign plugins
        $otherPlugin = null;
        $myPlugin = null;
        foreach($plugins as $plugin){
            switch($plugin->getName()){
                case $otherPluginName:
                    $otherPlugin = $plugin;
                    break;
                case $myPluginName:
                    $myPlugin = $plugin;
                    break;
            }
        }

        // Change InstalledAt-date if SwagCustomizedProducts was installed before this plugin
        if(!empty($otherPlugin)){
            if($otherPlugin->getInstalledAt() <= $myPlugin->getInstalledAt()){
                $pluginRepo->update(
                    array(
                        array(
                            'id' => $otherPlugin->getId(),
                            'installedAt' => $myPlugin->getInstalledAt()->add(new \DateInterval('PT1M'))->format('Y-m-d H:i:s.v')
                        )
                    ),
                    \Shopware\Core\Framework\Context::createDefaultContext()
                );
            }
        }
    }

    /**
     * Create Custom Field
     *
     * @param $installContext
     * @return void
     */
    private function addCustomField($installContext)
    {
        $context = $installContext->getContext();
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');

        $fieldIds = $this->checkCustomFieldExist($context);

        if (!$fieldIds) {
            $customFieldSetRepository->create([
                [
                    'name' => 'emcgn_no_variant_preselection_set',
                    'config' => [
                        'label' => [
                            'en-GB' => 'No variants Preselection',
                            'de-DE' => 'Keine Varianten Vorauswahl'
                        ]
                    ],
                    'customFields' => [
                        [
                            'name' => 'emcgn_no_variant_preselection_active',
                            'type' => CustomFieldTypes::SWITCH,
                            'config' => [
                                'label' => [
                                    'en-GB' => 'Deactivates the variants preselection for exactly this master article',
                                    'de-DE' => 'Deaktiviert die Varianten Vorauswahl für genau diesen Stammartikel'
                                ],
                                'customFieldPosition' => 1
                            ]
                        ]
                    ],
                    'relations' => [
                        ['entityName' => 'product']
                    ]
                ]
            ], $context);
        }
    }

    /**
     * Delete Custom Field
     *
     * @param UninstallContext $uninstallContext
     * @return void
     */
    private function removeCustomField(UninstallContext $uninstallContext)
    {
        $context = $uninstallContext->getContext();
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');

        $fieldIds = $this->checkCustomFieldExist($context);

        if ($fieldIds) {
            $customFieldSetRepository->delete(array_values($fieldIds->getData()), $context);
        }
    }

    /**
     * Check whether the custom field exists
     *
     * @param $context
     * @return IdSearchResult|null
     */
    private function checkCustomFieldExist($context): ?IdSearchResult
    {
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('name', ['emcgn_no_variant_preselection_set']));

        $ids = $customFieldSetRepository->searchIds($criteria, $context);

        return $ids->getTotal() > 0 ? $ids : null;
    }
}
