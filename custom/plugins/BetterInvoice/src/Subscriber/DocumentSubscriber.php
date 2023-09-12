<?php declare(strict_types=1);

namespace LZYT8\BetterInvoice\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Checkout\Document\Event\DocumentTemplateRendererParameterEvent;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium;
use LZYT8\BetterInvoice\Service\FetchService;
use SepaQr\Data;

class DocumentSubscriber implements EventSubscriberInterface 
{
    private FetchService $fetchService;

    public function __construct(FetchService $fetchService)
    {
        $this->fetchService = $fetchService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DocumentTemplateRendererParameterEvent::class => 'onDocumentTemplateRendererParameterEvent'
        ];
    }

    public function onDocumentTemplateRendererParameterEvent(DocumentTemplateRendererParameterEvent $event) : void 
    {
        $parameters = $event->getParameters();

        if ($parameters['config']['name'] != 'invoice' && $parameters['config']['name'] != 'rechnung')
            return;

        $order = $parameters['order'];
        $drop = $this->fetchService->fetchActiveDropFromOrder($order, $parameters['context']->getContext());

        $receiver = $drop ? $drop->getBank() : $parameters['config']['bankName'];
        $iban = $drop ? $drop->getIban() : $parameters['config']['bankIban'];
        $bic  = $drop ? $drop->getBic() : $parameters['config']['bankBic'];

        $paymentData = Data::create()
            ->setName($receiver)
            ->setIban($iban)
            ->setBic($bic)
            ->setAmount($order->getPrice()->getTotalPrice())
            ->setRemittanceText('Rechnungsnr. ' . $parameters['config']['custom']['invoiceNumber']);

        $qrDataUri = Builder::create()
            ->data((string)$paymentData)
            ->errorCorrectionLevel(new ErrorCorrectionLevelMedium())
            ->build()
            ->getDataUri();

        $event->setExtensions([
            'sepaQr' => $qrDataUri,
            'drop' => $drop
        ]);
    }
}