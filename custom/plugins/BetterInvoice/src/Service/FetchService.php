<?php declare(strict_types=1);

namespace LZYT8\BetterInvoice\Service;

use LZYT8\BetterInvoice\Core\Content\CustomDrop\CustomDropEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\AndFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextRestorer;
use Shopware\Core\Checkout\Order\OrderEntity as Order;
use Shopware\Core\Framework\Context;

class FetchService {

    private EntityRepositoryInterface $dropRepository;
    private SalesChannelContextRestorer $salesChannelContextRestorer;

    public function __construct(EntityRepositoryInterface $dropRepository, SalesChannelContextRestorer $salesChannelContextRestorer)
    {
        $this->dropRepository = $dropRepository;
        $this->salesChannelContextRestorer = $salesChannelContextRestorer;
    }

    public function fetchActiveDrop(Context $context) : ?CustomDropEntity
    {
        $criteria = new Criteria();
        $criteria->addAssociation('rule');
        $criteria->addSorting(new FieldSorting('priority', FieldSorting::ASCENDING));
        $criteria->setLimit(1);

        $criteria->addFilter(new AndFilter([
            new EqualsFilter('enabled', true),
            new EqualsAnyFilter('ruleId', $context->getRuleIds())
        ]));

        return $this->dropRepository->search($criteria, $context)->first();
    }

    public function fetchActiveDropFromOrder(Order $order, Context $context) : ?CustomDropEntity {
        $salesChannelContext = $this->salesChannelContextRestorer->restoreByOrder($order->getId(), $context);
        return $this->fetchActiveDrop($salesChannelContext->getContext());
    }

}