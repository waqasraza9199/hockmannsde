<?php

declare(strict_types=1);

namespace RHWeb\ThemeFeatures\Subscriber;

use Shopware\Core\Content\Cms\SalesChannel\SalesChannelCmsPageLoaderInterface;
use Shopware\Core\Content\Cms\SalesChannel\SalesChannelCmsPageRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Storefront\Pagelet\Header\HeaderPageletLoadedEvent;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class CmsLoaderSubscriber implements EventSubscriberInterface
{
    private $systemConfigService;
    private $cmsPageRepository;
    private $cmsPageLoader;


    public function __construct(
        SalesChannelCmsPageRepository $cmsPageRepository,
        SystemConfigService $systemConfigService,
        SalesChannelCmsPageLoaderInterface $cmsPageLoader)
    {
        $this->cmsPageRepository = $cmsPageRepository;
        $this->systemConfigService = $systemConfigService;
        $this->cmsPageLoader = $cmsPageLoader;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            HeaderPageletLoadedEvent::class => 'getCmsPage'
        ];
    }



    public function getCmsPage(HeaderPageletLoadedEvent $event): void
    {

        $request = $event->getRequest();
        $context = $event->getSalesChannelContext();
        $id = $this->systemConfigService->get('RHWebThemeFeatures.config.RhwebThemeFeaturesExitIntentCmsPageId');

        if($id != ''){
            $criteria = new Criteria([$id]);
            $pages = $this->cmsPageLoader->load($request, $criteria, $context);

            $event->getPagelet()->assign([
                'RHWebCmsPage' => $pages->get($id)
            ]);
        }
    }
}
