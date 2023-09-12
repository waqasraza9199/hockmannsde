<?php declare(strict_types=1);

namespace ZaibaSandMailToValidOrders\Exception;

use RuntimeException;
use Throwable;

/**
 * @since 1.0.0
 *
 * @inheritdoc
 */
class ResendOrderConfirmationMailException extends RuntimeException
{
    /**
     * @since 1.0.0
     *
     * @param string $orderId
     * @param string $message
     * @param Throwable|null $previous
     */
    public function __construct(string $orderId, string $message, Throwable $previous = null)
    {
        parent::__construct(
            sprintf(
                'Failed to resend order confirmation for %s: %s',
                $orderId,
                $message
            ),
            0,
            $previous
        );
    }
}
