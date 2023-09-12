<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swag\PayPal\Util\Availability;

use Shopware\Core\Framework\Struct\Struct;

class AvailabilityContext extends Struct
{
    protected string $billingCountryCode;

    protected string $currencyCode;

    protected float $totalAmount;

    public function getBillingCountryCode(): string
    {
        return $this->billingCountryCode;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }
}
