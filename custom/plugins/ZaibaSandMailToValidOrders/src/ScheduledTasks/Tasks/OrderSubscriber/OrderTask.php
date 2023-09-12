<?php declare(strict_types=1);

namespace ZaibaSandMailToValidOrders\ScheduledTasks\Tasks\OrderSubscriber;

use ZaibaSandMailToValidOrders\ScheduledTasks\Handlers\OrderSubscriber\OrderTaskHandler;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class OrderTask extends ScheduledTask
{
    /**
     * @return string
     */
    public static function getTaskName(): string
    {
        return 'zaiba.sand_mail_to_valid_orders_task';
    }

    /**
     * @return int
     */
    public static function getDefaultInterval(): int
    {
//        return 60; # 1 minute
        return (60*60*24); # 24 hours
    }
}
