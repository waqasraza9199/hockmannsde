<?php declare(strict_types=1);

namespace ZaibaNotifyAdminIfSMTPNotWorking\ScheduledTask;

use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Core\Content\Mail\Service\AbstractMailService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Symfony\Component\HttpFoundation\ParameterBag;

class ExampleTaskHandler extends ScheduledTaskHandler
{
    protected  AbstractMailService $mailService;

    protected  EntityRepositoryInterface $salesChannelRepository;

    protected SystemConfigService $systemConfigService;

    public function __construct(
        EntityRepositoryInterface $scheduledTaskRepository,
        AbstractMailService $mailService,
        EntityRepositoryInterface $salesChannelRepository,
        SystemConfigService $systemConfigService
    )
    {
        $this->mailService = $mailService;
        $this->salesChannelRepository = $salesChannelRepository;
        $this->systemConfigService = $systemConfigService;

        parent::__construct($scheduledTaskRepository);
    }

    public static function getHandledMessages(): iterable
    {
        return [ExampleTask::class];
    }

    public function run(): void
    {
        $context = Context::createDefaultContext();
        $salesChannel = $this->salesChannelRepository->search(new Criteria(), $context)->first();

        $data = new ParameterBag();
        $data->set(
            'recipients',
            [
                'waqasraza9199@gmail.com' => 'John Doe'
            ]
        );

        $data->set('senderName', 'I am the Sender');

        $data->set('contentHtml', 'Foo bar');
        $data->set('contentPlain', 'Foo bar');
        $data->set('subject', 'The subject');
        $data->set('salesChannelId', $salesChannel->getId());

        try{
            $this->mailService->send(
                $data->all(),
                $context,
            );
            $this->systemConfigService->set('ZaibaNotifyAdminIfSMTPNotWorking.config.smtpError', false);
            $this->systemConfigService->delete('ZaibaNotifyAdminIfSMTPNotWorking.config.smtpErrorMessage');

        }catch (\Exception $exception ){
            $this->systemConfigService->set('ZaibaNotifyAdminIfSMTPNotWorking.config.smtpError', true);
            $this->systemConfigService->set('ZaibaNotifyAdminIfSMTPNotWorking.config.smtpErrorMessage', $exception->getMessage());
            // catch exception - otherwise the task will never be called again
        }
    }

}
