<?php declare(strict_types=1);

namespace ZaibaNotifyAdminIfSMTPNotWorking\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class ExampleTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'zaiba.example_task';
    }

    public static function getDefaultInterval(): int
    {
        return (60*60*1); // 1 hour
    }
}
