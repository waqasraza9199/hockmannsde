<?php declare(strict_types=1);

namespace LZYT8\BetterInvoice\Service;

use LZYT8\BetterInvoice\Core\Content\CustomDrop\CustomDropEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Content\Mail\Service\AbstractMailService;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\AndFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Core\Content\MailTemplate\MailTemplateTypes;
use Shopware\Core\Checkout\Order\OrderEntity as Order;
use LZYT8\BetterInvoice\Service\FetchService;
use Shopware\Core\Framework\Api\Context\AdminApiSource;
use LZYT8\BetterInvoice\Service\LicenseService;
use Shopware\Core\Framework\Context;
use Symfony\Component\Mime\Email;

class AddDataToMails extends AbstractMailService
{
    private AbstractMailService $mailService;

    private EntityRepositoryInterface $mailTemplateRepository;

    private EntityRepositoryInterface $orderRepository;

    private EntityRepositoryInterface $mediaRepository;

    private SystemConfigService $systemConfigService;

    private FetchService $fetchService;

    private LicenseService $licenseService;

    public function __construct(
        AbstractMailService $mailService,
        EntityRepositoryInterface $mailTemplateRepository,
        EntityRepositoryInterface $orderRepository,
        EntityRepositoryInterface $mediaRepository,
        SystemConfigService $systemConfigService,
        FetchService $fetchService,
        LicenseService $licenseService
    ) {
        $this->mailService = $mailService;
        $this->mailTemplateRepository = $mailTemplateRepository;
        $this->orderRepository = $orderRepository;
        $this->mediaRepository = $mediaRepository;
        $this->systemConfigService = $systemConfigService;
        $this->fetchService = $fetchService;
        $this->licenseService = $licenseService;
    }

    public function getDecorated(): AbstractMailService
    {
        return $this->mailService;
    }

    public function send(array $data, Context $context, array $templateData = []): ?Email
    {  
        $source = $context->getSource();

        if (!($source instanceof AdminApiSource)) {
            $isValid = $this->licenseService->isValid($source->getSalesChannelId());
            if (!$isValid)
                return $this->mailService->send($data, $context, $templateData);
        }

        if (isset($templateData['order'])) 
        {
            $order = $templateData['order'];
            if (!$order instanceof Order)
                $order = $this->orderRepository->search(new Criteria([$templateData['order']['id']]), $context)->first();

            $drop = $this->fetchService->fetchActiveDropFromOrder($order, $context);

            $templateData['lzyt_drop'] = $drop;
        }

        if (isset($data['templateId']) && $this->isOrderConfirmationMailTemplate($data['templateId'], $context)) 
        {
            $mailText = $this->systemConfigService->get('BetterInvoice.config.mailTemplateText');
            $mailHtml = $this->systemConfigService->get('BetterInvoice.config.mailTemplateHtml');

            if (!empty($mailText))
                $data['contentPlain'] = $mailText;

            if (!empty($mailHtml))
                $data['contentHtml'] = $mailHtml;
        }

        if (isset($data['templateId']) && $this->isInvoiceMailTemplate($data['templateId'], $context)) 
        {
            $mailText = $this->systemConfigService->get('BetterInvoice.config.invoiceMailTemplateText');
            $mailHtml = $this->systemConfigService->get('BetterInvoice.config.invoiceMailTemplateHtml');

            if (!empty($mailText))
                $data['contentPlain'] = $mailText;

            if (!empty($mailHtml))
                $data['contentHtml'] = $mailHtml;
        }

        return $this->mailService->send($data, $context, $templateData);
    }

    private function isOrderConfirmationMailTemplate(string $uid, Context $context) : bool 
    {
        $criteria = new Criteria([$uid]);
        $criteria->addAssociation('mailTemplateType');
        $mailTemplate = $this->mailTemplateRepository->search($criteria, $context)->first();

        return $mailTemplate->getMailTemplateType()->getTechnicalName() == MailTemplateTypes::MAILTYPE_ORDER_CONFIRM;
    }

    private function isInvoiceMailTemplate(string $uid, Context $context) : bool 
    {
        $criteria = new Criteria([$uid]);
        $criteria->addAssociation('mailTemplateType');
        $mailTemplate = $this->mailTemplateRepository->search($criteria, $context)->first();

        return $mailTemplate->getMailTemplateType()->getTechnicalName() == MailTemplateTypes::MAILTYPE_DOCUMENT_INVOICE;
    }
}