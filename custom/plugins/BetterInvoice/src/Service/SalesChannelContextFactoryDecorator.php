<?php declare(strict_types=1);

namespace LZYT8\BetterInvoice\Service;

use Shopware\Core\Framework\Context;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SalesChannel\Context\AbstractSalesChannelContextFactory;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\SumAggregation;
use LZYT8\BetterInvoice\Core\Extension\SalesChannelContextExtension;

class SalesChannelContextFactoryDecorator extends AbstractSalesChannelContextFactory
{
    private EntityRepositoryInterface $orderRepository;

    private AbstractSalesChannelContextFactory $decoratedService;

    public function __construct(
        AbstractSalesChannelContextFactory $salesChannelContextFactory,
        EntityRepositoryInterface $orderRepository
    ) {
        $this->decoratedService = $salesChannelContextFactory;
        $this->orderRepository = $orderRepository;
    }

    public function getDecorated(): AbstractSalesChannelContextFactory
    {
        return $this->decoratedService;
    }

    public function create(string $token, string $salesChannelId, array $options = []): SalesChannelContext
    {
        $salesChannelContext = $this->decoratedService->create($token, $salesChannelId, $options);
        $totalShopSaleAmount = $this->calculateTotalShopSaleAmount($salesChannelContext->getContext());
        
        $extension = new SalesChannelContextExtension();
        $extension->setTotalShopSalesAmount($totalShopSaleAmount);
        
        $salesChannelContext->addExtension(SalesChannelContextExtension::KEY, $extension);

        return $salesChannelContext;
    }

    private function calculateTotalShopSaleAmount(Context $context) : float 
    {
        $criteria = new Criteria();
        $criteria->addAggregation(new SumAggregation('order-total-sum', 'amountTotal'));

        $orders = $this->orderRepository->search($criteria, $context);
        return $orders->getAggregations()->get('order-total-sum')->getSum();
    }
}