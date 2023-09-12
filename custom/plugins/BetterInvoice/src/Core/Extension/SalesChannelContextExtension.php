<?php declare(strict_types=1);

namespace LZYT8\BetterInvoice\Core\Extension;

use Shopware\Core\Framework\Struct\Struct;

class SalesChannelContextExtension extends Struct 
{
    public const KEY = 'lzyt-saleschannelcontext-ext';

    protected $totalShopSalesAmount = 0;

    public function getTotalShopSalesAmount() : float 
    {
        return $this->totalShopSalesAmount;
    }

    public function setTotalShopSalesAmount(float $value) : void 
    {
        $this->totalShopSalesAmount = $value;
    }
}