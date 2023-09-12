<?php declare(strict_types=1);

namespace LZYT8\BetterInvoice\Core\Rule;

use Shopware\Core\Framework\Rule\Rule;
use Shopware\Core\Framework\Rule\RuleScope;
use Shopware\Core\Framework\Rule\RuleConstraints;
use Shopware\Core\Checkout\CheckoutRuleScope;

class WhatsAppRule extends Rule
{
    protected bool $isAvailable;

    public function __construct()
    {
        parent::__construct();
        $this->isAvailable = false;
    }

    public function getName(): string
    {
        return 'lzyt_whatsapp_available';
    }

    public function match(RuleScope $scope): bool
    {
        if (!$scope instanceof CheckoutRuleScope)
            return false;

        $customer = $scope->getSalesChannelContext()->getCustomer();
        if (empty($customer))
            return false;
 
        
    }

    public function getConstraints(): array
    {
        return [
            'isAvailable' => RuleConstraints::bool(),
        ];
    }
}