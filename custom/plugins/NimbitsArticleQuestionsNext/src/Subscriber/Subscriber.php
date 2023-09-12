<?php declare(strict_types=1);

namespace Nimbits\NimbitsArticleQuestionsNext\Subscriber;

use Nimbits\NimbitsArticleQuestionsNext\Setting\Service\SettingService;
use Shopware\Core\Content\Mail\Service\AbstractMailService;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Struct\StructCollection;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Shopware\Storefront\Pagelet\Footer\FooterPageletLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class Subscriber implements EventSubscriberInterface
{

    /** @var EntityRepositoryInterface $articleQuestionsRepository */
    protected $articleQuestionsRepository;
    private $settingsService;
    private $mailService;
    private $salesChannelRepository;
    private $twig;

    public function __construct(
        SettingService            $settingsService,
        EntityRepositoryInterface $articleQuestionsRepository,
        AbstractMailService       $mailService,
        EntityRepositoryInterface $salesChannelRepository,
        \Twig\Environment         $twig,
        EntityRepositoryInterface $productRepository
    )
    {
        $this->settingsService = $settingsService;
        $this->articleQuestionsRepository = $articleQuestionsRepository;
        $this->mailService = $mailService;
        $this->salesChannelRepository = $salesChannelRepository;
        $this->twig = $twig;
        $this->productRepository = $productRepository;

    }

    public static function getSubscribedEvents(): array
    {
        // Return the events to listen to as array like this:  <event to listen to> => <method to execute>
        return [
            ProductPageLoadedEvent::class => 'onProductPageLoaded',
            FooterPageletLoadedEvent::class => 'onFooterLoaded'
        ];
    }

    public function onFooterLoaded(FooterPageletLoadedEvent $event)
    {
        $event->getPagelet()->addExtension('nimbitsArticleQuestionsSettings',
            $this->settingsService->getSettingsAsStruct($event->getSalesChannelContext()->getSalesChannel()->getId())
        );

    }

    public function onProductPageLoaded(ProductPageLoadedEvent $event)
    {
        $this->checkSalesChannel($event);

        $productId = $event->getPage()->getProduct()->getId();

        $event->getPage()->getProduct()->addExtension('nimbitsArticleQuestionsSettings', $this->settingsService->getSettingsAsStruct($event->getSalesChannelContext()->getSalesChannel()->getId()));

        $criteria = (new Criteria)
            ->addFilter(new EqualsFilter('article_id', $productId))
            ->addFilter(new EqualsFilter('active', 1));

        $settingsarr = $this->settingsService->getSettingsAsArray();
        if(array_key_exists("onlyShowCurrentLanguage", $settingsarr)){
             if ($settingsarr['onlyShowCurrentLanguage']) {
                 $criteria->addFilter(new EqualsFilter('language_id', $event->getContext()->getLanguageId()));
             }
        }



        $questions = $this->articleQuestionsRepository->search(
            $criteria,
            \Shopware\Core\Framework\Context::createDefaultContext()
        );

        $questions = $questions->getElements();


        $event->getPage()->getProduct()->addExtension('nimbitsArticleQuestions', (new StructCollection())->assign(['questions' => $questions]));
    }

    public function checkSalesChannel(ProductPageLoadedEvent $event)
    {

        $productId = $event->getPage()->getProduct()->getId();

        $criteria = (new Criteria)
            ->addFilter(new EqualsFilter('article_id', $productId))
            ->addFilter(new EqualsFilter('active', 1));

        $settingsarr = $this->settingsService->getSettingsAsArray();

        $allowedStorefronts = [];
        if(array_key_exists("defaultQuestionVisibilities", $settingsarr))
        {
            $allowedStorefronts = $settingsarr['defaultQuestionVisibilities'];
        }

        $currentStorefront = $event->getContext()->getSource()->getSalesChannelId();
        $showInSalesChannel = false;

        foreach ($allowedStorefronts as $allowedStorefront) {
            if ($currentStorefront == $allowedStorefront) {
                $showInSalesChannel = true;
            }
            $event->getPage()->addExtension('nimbitsArticleQuestions', (new StructCollection())->assign(['showInSalesChannel' => $showInSalesChannel]));
        }

    }


}
