<?php declare(strict_types=1);

namespace Lenz\GoogleShopping;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Shopware\Core\System\CustomField\Aggregate\CustomFieldSet\CustomFieldSetEntity;
use Shopware\Core\System\CustomField\CustomFieldTypes;

class LenzPlatformGoogleShopping extends Plugin
{
    public function install(InstallContext $installContext): void
    {
        foreach (array_keys($this->customFieldSets) as $key)
        {
            $this->createOrUpdateCustomFields($key);
        }
    }

    public function activate(ActivateContext $activateContext): void
    {
        foreach (array_keys($this->customFieldSets) as $key)
        {
            $this->createOrUpdateCustomFields($key);
        }
    }

    public function update(UpdateContext $updateContext): void
    {
        foreach (array_keys($this->customFieldSets) as $key)
        {
            $this->createOrUpdateCustomFields($key);
        }
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        if (!$uninstallContext->keepUserData()) {
            $connection = $this->container->get(Connection::class);

            $tablesToDelete = [
                'lenz_google_shopping_taxonomy_translation',
                'lenz_google_shopping_taxonomy',
            ];

            foreach ($tablesToDelete as $table) {
                try {
                    $connection->executeStatement('DROP TABLE IF EXISTS `' . $table . '`');
                } catch (\Exception $e) {
                    echo "Table \"" . $table . "\" not deleted.\n\r";
                }
            }

            foreach (array_keys($this->customFieldSets) as $key)
            {
                $this->deleteCustomFieldSet($key);
            }
        }
    }

    // -- Custom fields
    private array $customFieldSets = [
        'lenz_google_shopping_category' => [
            'name' => 'lenz_google_shopping_category',
            'config' => [
                'label' => [
                    'en-GB' => 'Google Shopping - Category',
                    'de-DE' => 'Google Shopping - Kategorie',
                ],
            ],
            'relations' => [
                ['entityName' => 'category']
            ],
            'customFields' => [
                [
                    'name' => 'lenz_google_shopping_category_taxonomy',
                    'type' => CustomFieldTypes::TEXT,
                    'config' => [
                        'label' => [
                            'en-GB' => 'Taxonomy',
                            'de-DE' => 'Taxonomie'
                        ],
                        'componentName' => 'sw-field',
                        'customFieldPosition' => 101,
                        'type' => CustomFieldTypes::TEXT,
                        'customFieldType' => CustomFieldTypes::TEXT,
                    ],
                ],
            ],
        ],
    ];

    private function findCustomFieldSet($name): ?CustomFieldSetEntity
    {
        /** @var EntityRepositoryInterface $customFieldSetRepository */
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('name', $name));
        $criteria->addAssociation('customFields');
        $criteria->addAssociation('relations');

        /** @var CustomFieldSetEntity|null $customFieldSet */
        return $customFieldSetRepository->search($criteria, Context::createDefaultContext())->first();
    }

    public function createOrUpdateCustomFields($name)
    {
        /** @var EntityRepositoryInterface $customFieldSetRepository */
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');
        $customFieldSet = $this->findCustomFieldSet($name);

        $customFieldSetId = null;
        $customFieldName2Id = [];
        $relationEntity2Id = [];
        if(!empty($customFieldSet)) {
            $customFieldSetId = $customFieldSet->getId();

            foreach ($customFieldSet->getCustomFields() as $customField) {
                $customFieldName2Id[$customField->getName()] = $customField->getId();
            }
            foreach ($customFieldSet->getRelations() as $relation) {
                $relationEntity2Id[$relation->getEntityName()] = $relation->getId();
            }
        }

        $customFieldSet = $this->customFieldSets[$name];
        $customFieldSet['id'] = $customFieldSetId;

        foreach ($customFieldSet['customFields'] as $customFieldKey => $customField) {
            if(!array_key_exists($customField['name'], $customFieldName2Id)) {
                continue;
            }

            $customFieldSet['customFields'][$customFieldKey]['id'] = $customFieldName2Id[$customField['name']];
        }

        foreach ($customFieldSet['relations'] as $relationKey => $relation) {
            if(!array_key_exists($relation['entityName'], $relationEntity2Id)) {
                continue;
            }

            $customFieldSet['relations'][$relationKey]['id'] = $relationEntity2Id[$relation['entityName']];
        }

        $customFieldSetRepository->upsert(
            [ $customFieldSet ],
            Context::createDefaultContext()
        );
    }

    private function deleteCustomFieldSet(string $name)
    {
        /** @var EntityRepositoryInterface $customFieldSetRepository */
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');

        $customFieldSet = $this->findCustomFieldSet($name);

        if ($customFieldSet === null) {
            return;
        }

        $customFieldSetRepository->delete(
            [
                ['id' => $customFieldSet->getId()],
            ],
            Context::createDefaultContext()
        );
    }
}
