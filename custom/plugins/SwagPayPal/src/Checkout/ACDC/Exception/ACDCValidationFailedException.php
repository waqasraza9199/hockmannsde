<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swag\PayPal\Checkout\ACDC\Exception;

use Shopware\Core\Checkout\Payment\Exception\SyncPaymentProcessException;

class ACDCValidationFailedException extends SyncPaymentProcessException
{
    public function __construct(
        string $orderTransactionId,
        ?string $message = null
    ) {
        parent::__construct($orderTransactionId, $message ?? 'Credit card validation failed, 3D secure was not validated.');
    }
}
