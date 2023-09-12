<?php declare(strict_types=1);

namespace Cogi\Tags\Subscriber;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StorefrontSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityRepositoryInterface
     */
    protected $tagRepository;

    public function __construct(
        EntityRepositoryInterface $tagRepository
    )
    {
        $this->tagRepository = $tagRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductPageLoadedEvent::class => 'onProductPageLoadedEvent'
        ];
    }

    public function onProductPageLoadedEvent(ProductPageLoadedEvent $event): void
    {
        $tagIds = $event->getPage()->getProduct()->getTagIds();
        if (empty($tagIds)) {
            return;
        }
        $criteria = new Criteria($tagIds);

        /** @var TagCollection $tags */
        $tags = $this->tagRepository->search($criteria, $event->getContext())->getEntities();

        $event->getPage()->getProduct()->addExtension('cogi-tags', $tags);
    }
}
