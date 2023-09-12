<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swag\PayPal\Test\Checkout\Method;

use Swag\PayPal\Checkout\Payment\Method\VenmoHandler;

/**
 * @internal
 */
class VenmoHandlerTest extends AbstractSyncAPMHandlerTest
{
    protected function getPaymentHandlerClassName(): string
    {
        return VenmoHandler::class;
    }
}
