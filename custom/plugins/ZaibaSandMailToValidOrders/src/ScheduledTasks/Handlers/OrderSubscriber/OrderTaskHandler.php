<?php declare(strict_types=1);

namespace ZaibaSandMailToValidOrders\ScheduledTasks\Handlers\OrderSubscriber;

use Shopware\Core\System\SystemConfig\SystemConfigService;
use ZaibaSandMailToValidOrders\ScheduledTasks\Tasks\OrderSubscriber\OrderTask;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Doctrine\DBAL\Connection;
use ZaibaSandMailToValidOrders\Services\MailService;

class OrderTaskHandler extends ScheduledTaskHandler
{
    /**
     * @var EntityRepositoryInterface
     */
    protected EntityRepositoryInterface $orderRepository;

    protected MailService $mailService;

    protected Connection $connection;

    protected SystemConfigService $systemConfigService;


    /**
     * @param EntityRepositoryInterface $scheduledTaskRepository
     * @param EntityRepositoryInterface $orderRepository
     */
    public function __construct(
        EntityRepositoryInterface $scheduledTaskRepository,
        EntityRepositoryInterface $orderRepository,
        MailService $mailService,
        Connection $connection,
        SystemConfigService $systemConfigService
    )
    {
        $this->orderRepository = $orderRepository;
        $this->mailService = $mailService;
        $this->connection = $connection;
        $this->systemConfigService = $systemConfigService;


        parent::__construct($scheduledTaskRepository);
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        return [OrderTask::class];
    }

    /**
     * Gets all waiting Stock subscriptions, checks for each if the subscriber should be notified and notifies the
     * subscriber
     *
     * @throws Throwable
     */
    public function run(?OutputInterface $output = null, int $verbosity = OutputInterface::VERBOSITY_QUIET): void
    {
        $context = Context::createDefaultContext();

        $orders = $this->getOrders($context);

        if(empty($orders)){
            return;
        }

        $mailTemplateID = $this->systemConfigService->get('ZaibaSandMailToValidOrders.config.mailTemplateID');

        if(!$mailTemplateID){
            return;
        }

        foreach ($orders as $order){

            if($order['custom_fields']){
                $customFields = json_decode($order['custom_fields'], true);
                if(isset($customFields['resentEmail']) and $customFields['resentEmail'] == true){
                    continue;
                }

                if(isset($customFields['dunp_geocode']) and $customFields['dunp_geocode'] == false){
                    continue;
                }
            }

            $id = Uuid::fromBytesToHex($order['id']);
            $this->sendOrderEmail($id, $mailTemplateID, $context);
        }
    }

    public function getOrders(Context $context)
    {
        $today = new \DateTime();
        $today = $today->setTimezone(new \DateTimeZone('UTC'));
        $startDate = $today->format('Y-m-d H:i:s');

//        $yesterday = new \DateTime('-2 days');
        $yesterday = new \DateTime('-48 hours');
        $yesterday = $yesterday->setTimezone(new \DateTimeZone('UTC'));
        $endDate = $yesterday->format('Y-m-d H:i:s');


        $orders =$this->connection->executeQuery("select * from `order` where `created_at` between '$endDate' and  '$startDate'")->fetchAllAssociative();

        return $orders;
    }

    private function sendOrderEmail(string $orderId, string $mailTemplateID, Context $context)
    {
        $this->mailService->resend($orderId, $mailTemplateID, $context);
    }

    private function updateOrder(string $orderId, $customFields)
    {
        $customFields = json_encode($customFields);
        $orders =$this->connection->executeStatement("update `order` set custom_fields = '$customFields' where  `id` = UUID_TO_BIN('$orderId')");
    }
}

