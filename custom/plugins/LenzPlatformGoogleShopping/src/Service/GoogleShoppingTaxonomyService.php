<?php
namespace Lenz\GoogleShopping\Service;

use Lenz\GoogleShopping\Core\Content\GoogleShopping\Taxonomy\TaxonomyEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\Language\LanguageEntity;

class GoogleShoppingTaxonomyService
{
    /**
     * @var EntityRepositoryInterface
     */
    private $googleShoppingTaxonomyRepository;
    /**
     * @var EntityRepositoryInterface
     */
    private $languageRepository;

    public function __construct(EntityRepositoryInterface $googleShoppingTaxonomyRepository, EntityRepositoryInterface $languageRepository)
    {

        $this->googleShoppingTaxonomyRepository = $googleShoppingTaxonomyRepository;
        $this->languageRepository = $languageRepository;
    }

    public function import()
    {
        $files = [
            'en-GB' => __DIR__ . '/../Resources/google_taxonomy/taxonomy-with-ids.en-GB.txt',// Default language has to be first!
            'de-DE' => __DIR__ . '/../Resources/google_taxonomy/taxonomy-with-ids.de-DE.txt',
        ];
        $aInsertData = [];

        // Load language codes.
        $criteria = new Criteria();
        $criteria->addAssociation('translationCode');
        $languages = $this->languageRepository->search($criteria, Context::createDefaultContext());

        $languageTranslation = [];
        /** @var LanguageEntity $language */
        foreach ($languages as $language) {
            $languageTranslation[$language->getTranslationCode()->getCode()] = $language->getId();
        }

        $allTaxonomiesResult = $this->googleShoppingTaxonomyRepository->search(new Criteria(), Context::createDefaultContext());

        $aTaxonomyCat2Id = [];
        /** @var TaxonomyEntity $item */
        foreach ($allTaxonomiesResult as $item) {
            $aTaxonomyCat2Id[$item->getCatId()] = $item->getId();
        }
        unset($allTaxonomiesResult);

        foreach ($files as $languageCode => $file) {
            $sData = \file_get_contents($file);

            $aLines = \explode("\n", $sData);
            foreach($aLines as $key => $sLine) {
                if(strpos($sLine, ' - ') === false) {
                    continue;
                }
                $aLineData = \explode(' - ', $sLine, 2);
                $categoryId = intval(trim($aLineData[0]));
                $name = trim($aLineData[1]);

                if(!array_key_exists($categoryId, $aInsertData)) {
                    // Add data that is used for all translations.
                    $aInsertData[$categoryId] = [
                        'catId' => $categoryId,
                        'translations' => [
                            Defaults::LANGUAGE_SYSTEM => [
                                'name' => $name,
                            ],
                        ],
                    ];

                    if(array_key_exists($categoryId, $aTaxonomyCat2Id)) {
                        $aInsertData[$categoryId]['id'] = $aTaxonomyCat2Id[$categoryId];
                    }
                }

                $aInsertData[$categoryId]['translations'][$languageTranslation[$languageCode]] = [
                    'name' => $name,
                ];
            }
        }

        $aInsertData = array_values($aInsertData);

        $this->googleShoppingTaxonomyRepository->upsert(
            $aInsertData,
            Context::createDefaultContext()
        );
    }
}
