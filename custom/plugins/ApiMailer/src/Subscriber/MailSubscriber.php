<?php declare(strict_types=1);

namespace LZYT8\ApiMailer\Subscriber;

use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mime\Email;
use Shopware\Core\Framework\Context;
use LZYT8\ApiMailer\Service\SendService;
use LZYT8\ApiMailer\Service\LicenseService;
use LZYT8\ApiMailer\Service\ConfigurationService;
use Shopware\Core\Framework\Api\Context\AdminApiSource;
use Shopware\Core\Content\Mail\Service\MailAttachmentsBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Content\MailTemplate\Service\Event\MailBeforeSentEvent;
use Shopware\Core\Content\MailTemplate\Subscriber\MailSendSubscriberConfig;

class MailSubscriber implements EventSubscriberInterface {

    private LoggerInterface $logger;
    private SendService $sendService;
    private LicenseService $licenseService;
    private ConfigurationService $configurationService;
    private MailAttachmentsBuilder $attachmentsBuilder;
    private EntityRepository $documentRepository;

    public function __construct(MailAttachmentsBuilder $attachmentsBuilder, EntityRepository $documentRepository, ConfigurationService $configurationService, LicenseService $licenseService, SendService $sendService, LoggerInterface $logger) 
    {
        $this->logger = $logger;
        $this->attachmentsBuilder = $attachmentsBuilder;
        $this->licenseService = $licenseService;
        $this->sendService = $sendService;
        $this->configurationService = $configurationService;
        $this->documentRepository = $documentRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MailBeforeSentEvent::class => 'onBeforeSend'
        ];
    }

    public function onBeforeSend(MailBeforeSentEvent $event): void
    {
        if ($this->configurationService->propagadeDisabled())
            $event->stopPropagation();

        $source = $event->getContext()->getSource();

        if (!($source instanceof AdminApiSource)) {
            $isValid = $this->licenseService->isValid($event->getContext()->getSource()->getSalesChannelId());
            if (!$isValid) {
                $this->logger->warning('License is not valid and therefore canceled sending');
                return;
            }
        }

        $attachments = [];
        $email = $event->getMessage();
        $config = $email->getMailAttachmentsConfig();

        if ($config != null) {
            $attachments = $this->attachmentsBuilder->buildAttachments(
                $config->getContext(),
                $config->getMailTemplate(),
                $config->getExtension(),
                $config->getEventConfig(),
                $config->getOrderId()
            );

            foreach ($attachments as $attachment) {
                $email->attach(
                    $attachment['content'],
                    $attachment['fileName'],
                    $attachment['mimeType']
                );
            }
        }

        $statusCode = $this->sendService->send($this->getPayload($email));

        if ($statusCode != 200) 
            throw new Exception('Canceled sending ... look at the logs for more information.');

        if (!empty($attachments))
            $this->setDocumentsSent($attachments, $config->getExtension(), $config->getContext());
    }

    private function getPayload(Email $email): array
    {
        $headers = $email->getHeaders();
        $html = $email->getHtmlBody();
        
        if (null !== $html && \is_resource($html)) {
            if (stream_get_meta_data($html)['seekable'] ?? false) {
                rewind($html);
            }
            $html = stream_get_contents($html);
        }

        [$attachments, $inlines, $html] = $this->prepareAttachments($email, $html);

        $payload = [
            'from' => $this->convertToPlainAddress($email->getFrom())[0],
            'to' => $this->convertToPlainAddress($email->getTo()),
            'subject' => $email->getSubject(),
            'attachment' => $attachments,
            'inline' => $inlines,
        ];

        if ($emails = $email->getCc())
            $payload['cc'] = $this->convertToPlainAddress($email->getCc());

        if ($emails = $email->getBcc())
            $payload['bcc'] = $this->convertToPlainAddress($email->getBcc());
        
        if ($email->getTextBody()) 
            $payload['text'] = $email->getTextBody();
        
        if ($html)
            $payload['html'] = $html;

        $headersToBypass = ['from', 'to', 'cc', 'bcc', 'subject', 'content-type'];

        foreach ($headers->all() as $name => $header) {
            if (\in_array($name, $headersToBypass, true))
                continue;

            if ($header instanceof TagHeader) {
                $payload[] = ['o:tag' => $header->getValue()];

                continue;
            }

            if ($header instanceof MetadataHeader) {
                $payload['v:'.$header->getKey()] = $header->getValue();

                continue;
            }

            $prefix = substr($name, 0, 2);

            if (\in_array($prefix, ['h:', 't:', 'o:', 'v:']) || \in_array($name, ['recipient-variables', 'template', 'amp-html']))
                $headerName = $header->getName();
            else
                $headerName = 'h:'.$header->getName(); 

            $payload[$headerName] = $header->getBodyAsString();
        }

        return $payload;
    }

    private function convertToPlainAddress(array $address): array {
        $plain = [];

        if (!empty($address)) {
            foreach($address as $obj) {
                $plain[] = $obj->getAddress();
            }
        }

        return $plain;
    }

    private function prepareAttachments(Email $email, ?string $html): array
    {
        $attachments = $inlines = [];

        foreach ($email->getAttachments() as $attachment) {
            $headers = $attachment->getPreparedHeaders();
            if ('inline' === $headers->getHeaderBody('Content-Disposition')) {
                if ($html) {
                    $filename = $headers->getHeaderParameter('Content-Disposition', 'filename');
                    $new = basename($filename);
                    $html = str_replace('cid:'.$filename, 'cid:'.$new, $html);

                    $p = new \ReflectionProperty($attachment, 'filename');
                    $p->setAccessible(true);
                    $p->setValue($attachment, $new);
                }
                $inlines[] = $attachment;
            } else {
                $attachments[] = $attachment;
            }
        }

        return [$attachments, $inlines, $html];
    }

    private function setDocumentsSent(array $attachments, MailSendSubscriberConfig $extension, Context $context): void
    {
        $documentAttachments = array_filter($attachments, fn (array $attachment) => \in_array($attachment['id'] ?? null, $extension->getDocumentIds(), true));
        $documentAttachments = array_column($documentAttachments, 'id');

        if (empty($documentAttachments))
            return;

        $payload = array_map(static fn (string $documentId) => [
            'id' => $documentId,
            'sent' => true,
        ], $documentAttachments);

        $this->documentRepository->update($payload, $context);
    }
}
