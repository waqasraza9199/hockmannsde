<?php declare(strict_types=1);

namespace LZYT8\BetterInvoice\Core\Rule;

use Shopware\Core\Framework\Rule\Rule;
use Shopware\Core\Framework\Rule\RuleScope;
use Shopware\Core\Framework\Rule\RuleConstraints;
use Symfony\Component\Validator\Constraints\Type;
use Shopware\Core\Framework\Rule\RuleComparison;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use LZYT8\BetterInvoice\Core\Extension\SalesChannelContextExtension;

class ShopSalesRule extends Rule
{
    protected float $amount;

    protected string $operator;

    public function __construct(string $operator = self::OPERATOR_EQ, ?float $amount = null)
    {
        parent::__construct();
        
        $this->operator = $operator;
        $this->amount = (float) $amount;
    }

    public function getName(): string
    {
        return 'lzyt_shop_sales';
    }

    public function match(RuleScope $scope): bool
    {
        $salesChannelContext = $scope->getSalesChannelContext();

        if (!$salesChannelContext->hasExtension(SalesChannelContextExtension::KEY))
            return false;

        $extension = $salesChannelContext->getExtension(SalesChannelContextExtension::KEY);
        return RuleComparison::numeric($extension->getTotalShopSalesAmount(), $this->amount, $this->operator);
    }

    public function getConstraints(): array
    {
        return [
            'amount' => RuleConstraints::float(),
            'operator' => RuleConstraints::numericOperators(false),
        ];
    }
}