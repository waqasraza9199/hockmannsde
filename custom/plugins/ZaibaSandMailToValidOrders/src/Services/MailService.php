<?php declare(strict_types=1);

namespace ZaibaSandMailToValidOrders\Services;

use ReflectionClass;
use ZaibaSandMailToValidOrders\Exception\ResendOrderConfirmationMailException;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Content\Mail\Service\AbstractMailService as SwagMailService;
use Shopware\Core\Content\MailTemplate\MailTemplateEntity;
use Shopware\Core\Content\MailTemplate\MailTemplateTypes;
use Shopware\Core\Content\MailTemplate\Service\Event\MailBeforeSentEvent;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @since 1.0.0
 *
 * @inheritdoc
 */
class MailService
{
    /**
     * @since 1.0.0
     *
     * @var EntityRepositoryInterface
     */
    private $orderRepository;

    /**
     * @since 1.0.0
     *
     * @var EntityRepositoryInterface
     */
    private $mailTemplateRepository;

    /**
     * @since 1.0.0
     *
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @since 1.0.0
     *
     * @var RequestStack
     */
    private $requestStack;


    /**
     * @var SwagMailService
     */
    protected $swMailService;


    /**
     * @param EntityRepositoryInterface $orderRepository
     * @param EntityRepositoryInterface $mailTemplateRepository
     * @param EventDispatcherInterface $eventDispatcher
     * @param RequestStack $requestStack
     * @param SwagMailService $swMailService
     */
    public function __construct(
        EntityRepositoryInterface $orderRepository,
        EntityRepositoryInterface $mailTemplateRepository,
        EventDispatcherInterface $eventDispatcher,
        RequestStack $requestStack,
        SwagMailService $swMailService

    ) {
        $this->orderRepository = $orderRepository;
        $this->mailTemplateRepository = $mailTemplateRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
        $this->swMailService = $swMailService;

    }

    /**
     * @since 1.0.0
     *
     * @param string $orderId
     * @param string|null $comment
     * @param Context $context
     */
    public function resend(string $orderId, string $mailTemplateID, Context $context): void
    {
        $order = $this->findOrder($orderId, $context);
        if ($order === null) {
            throw new ResendOrderConfirmationMailException($orderId, 'Order not found');
        }

        $this->sendMail($order, $mailTemplateID, $context);
    }

    /**
     * @since 1.0.0
     *
     * @param MailBeforeSentEvent $event
     */
    public function addCommentIfNotExistsToEvent(MailBeforeSentEvent $event): void
    {
        // This method only supports http based request due to missing Shopware extensibility.
        $masterRequest = $this->requestStack->getMasterRequest();
        if ($masterRequest === null || $this->isCli()) {
            return;
        }

        // Check if the event is for the order confirmation.
        /** @var string|null $mailTemplateId */
        $mailTemplateId = $event->getData()['templateId'] ?? null;
        if (empty($mailTemplateId) || !$this->isOrderConfirmationMail($mailTemplateId, $event->getContext())) {
            return;
        }

        // Check if there is any content for the comment.
        $comment = $this->sanitizeComment($masterRequest->request->get('comment'));
        if (empty($comment)) {
            return;
        }

        // Modify the message.
        $message = $event->getMessage();

        $htmlBody = $message->getHtmlBody();
        if (strpos($htmlBody, $comment) === false) {
            $message->html(sprintf('<div style="font-family:arial; font-size:12px;"><p>%s</p></div>%s', $comment, $htmlBody));
        }

        $textBody = $message->getTextBody();
        if (strpos($textBody, $comment) === false) {
            $message->text(sprintf("%s\n%s", $comment, $textBody));
        }
    }

    /**
     * @since 1.0.0
     *
     * @param Context $context
     * @param string $orderId
     *
     * @return OrderEntity
     */
    private function findOrder(string $orderId, Context $context): OrderEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $orderId));
        $criteria->addAssociation('salesChannel');
        $criteria->addAssociation('salesChannel.domains');
        $criteria->addAssociation('orderCustomer');
        $criteria->addAssociation('orderCustomer.salutation');
        $criteria->addAssociation('currency');
        $criteria->addAssociation('lineItems');
        $criteria->addAssociation('addresses');
        $criteria->addAssociation('addresses.country');
        $criteria->addAssociation('deliveries');
        $criteria->addAssociation('deliveries.shippingMethod');
        $criteria->addAssociation('deliveries.shippingOrderAddress');
        $criteria->addAssociation('deliveries.shippingOrderAddress.country');
        $criteria->addAssociation('deliveries.shippingCosts');
        $criteria->addAssociation('transactions');
        $criteria->addAssociation('transactions.paymentMethod');
        $criteria->setLimit(1);

        return $this->orderRepository->search($criteria, $context)->first();
    }

    /**
     * @since 1.0.0
     *
     * @param OrderEntity $order
     * @param string|null $comment
     * @param Context $context
     */

    private function sendMail(OrderEntity $order, string $mailTemplateID, Context $context): ?Email
    {
        $mailTemplateEntity = $this->getNotificationMailTemplate(
            $mailTemplateID,
            $context,
            $context->getLanguageId()
        );


        if (is_null($mailTemplateEntity)) {
            return null;
        }

        $mailData = $this->getBackInStockNotificationMailData($order, $mailTemplateEntity, $context);
        $templateData = $this->getBackInStockNotificationMailTemplateData($order, $mailTemplateEntity);


        return $this->swMailService->send(
            $mailData,
            $context,
            $templateData
        );

    }
//    private function sendMail(OrderEntity $order, ?string $comment, Context $context): void
//    {
//        $event = new CheckoutOrderPlacedEvent(
//            $this->modifyContext($context, $order),
//            $order,
//            $order->getSalesChannelId(),
//            $this->sanitizeComment($comment)
//        );
//
//        $this->eventDispatcher->dispatch($event);
//    }

    /**
     * @since 1.0.0
     *
     * @param string $templateId
     * @param Context $context
     *
     * @return bool
     */
    private function isOrderConfirmationMail(string $templateId, Context $context): bool
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $templateId));
        $criteria->addFilter(new EqualsFilter('mailTemplateType.technicalName', MailTemplateTypes::MAILTYPE_ORDER_CONFIRM));
        $criteria->setLimit(1);

        return $this->mailTemplateRepository->searchIds($criteria, $context)->getTotal() > 0;
    }

    /**
     * @since 1.0.0
     *
     * @param string|null $comment
     *
     * @return string|null
     */
    private function sanitizeComment(?string $comment): ?string
    {
        return $comment !== null && !empty(trim($comment)) ? sprintf('%s ', trim($comment)) : null;
    }

    /**
     * @since 1.0.0
     *
     * @return bool
     */
    private function isCli(): bool
    {
        return php_sapi_name() === 'cli';
    }

    /**
     * @since 1.1.0
     *
     * @param OrderEntity $order
     * @param Context $context
     *
     * @return Context
     *
     * @noinspection PhpDocMissingThrowsInspection
     */
    private function modifyContext(Context $context, OrderEntity $order): Context
    {
        if (!in_array($order->getLanguageId(), $context->getLanguageIdChain())) {
            $context = Context::createFrom($context);
            $reflection = new ReflectionClass($context);
            $property = $reflection->getProperty('languageIdChain');
            $property->setAccessible(true);
            $property->setValue($context, array_merge([$order->getLanguageId()], $context->getLanguageIdChain()));
        }

        return $context;
    }
    /**
     * @param string  $templateName
     * @param Context $context
     * @param string  $languageId
     *
     * @return MailTemplateEntity|null
     */
    private function getNotificationMailTemplate(string $templateid, Context $context, string $languageId): ?MailTemplateEntity
    {
        $languageContext = new Context(
            new SystemSource(),
            [],
            Defaults::CURRENCY,
            [$languageId, Defaults::LANGUAGE_SYSTEM],
            $context->getVersionId(),
            $context->getCurrencyFactor(),
            true
        );

        $criteria = new Criteria();
        $criteria->addAssociation('mail_template_type.technicalName');
        $criteria->addFilter( new EqualsFilter('id', $templateid) );

        $mailTemplateEntity = $this->mailTemplateRepository->search($criteria, $languageContext)->first();

        if (is_null($mailTemplateEntity)) {
            return null;
        }

        $mailTemplateEntity->setSenderName($mailTemplateEntity->getTranslation('senderName'));
        $mailTemplateEntity->setDescription($mailTemplateEntity->getTranslation('description'));
        $mailTemplateEntity->setSubject($mailTemplateEntity->getTranslation('subject'));
        $mailTemplateEntity->setContentHtml($mailTemplateEntity->getTranslation('contentHtml'));
        $mailTemplateEntity->setContentPlain($mailTemplateEntity->getTranslation('contentPlain'));

        return $mailTemplateEntity;
    }

    /**
     * @param StockSubscriber    $stockSubscriber
     * @param MailTemplateEntity $mailTemplate
     *
     * @return array
     */
    protected function getBackInStockNotificationMailData(OrderEntity $order, MailTemplateEntity $mailTemplate, Context $context): array
    {
        return [
            'recipients' => [
                $order->getOrderCustomer()->getEmail() =>  $order->getOrderCustomer()->getFirstName()
            ],
            'salesChannelId' => $order->getSalesChannelId(),
            'subject' => $mailTemplate->getSubject(),
            'senderName' => $mailTemplate->getSenderName(),
            'contentPlain' => $mailTemplate->getContentPlain(),
            'contentHtml' => $mailTemplate->getContentHtml(),
            'mediaIds' => []
        ];
    }

    /**
     * @param StockSubscriberProduct $stockSubscriberProduct
     * @param MailTemplateEntity     $mailTemplate
     *
     * @return array
     */
    protected function getBackInStockNotificationMailTemplateData(OrderEntity $order, MailTemplateEntity $mailTemplate): array
    {
        $templateData = $mailTemplate->jsonSerialize();
        $templateData['order'] = $order;

        return $templateData;
    }
}
