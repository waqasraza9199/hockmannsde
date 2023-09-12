<?php declare(strict_types=1);

namespace LZYT8\BetterInvoice\Subscriber;

use Shopware\Core\Framework\Context;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Storefront\Page\Checkout\Finish\CheckoutFinishPageLoadedEvent;
use LZYT8\BetterInvoice\Service\FetchService;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium;
use Shopware\Core\Checkout\Document\Service\DocumentConfigLoader;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Checkout\Document\DocumentEntity;
use SepaQr\Data;

class CheckoutFinishSubscriber implements EventSubscriberInterface 
{
    private FetchService $fetchService;
    private DocumentConfigLoader $documentConfigLoader;
    private EntityRepository $documentRepository;

    public function __construct(FetchService $fetchService, DocumentConfigLoader $documentConfigLoader, EntityRepository $documentRepository)
    {
        $this->fetchService = $fetchService;
        $this->documentConfigLoader = $documentConfigLoader;
        $this->documentRepository = $documentRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CheckoutFinishPageLoadedEvent::class => 'onCheckoutFinish'
        ];
    }

    public function onCheckoutFinish(CheckoutFinishPageLoadedEvent $event): void 
    {
        $salesChannelContext = $event->getSalesChannelContext();
        $context = $salesChannelContext->getContext();
        $order = $event->getPage()->getOrder();

        $invoice = $this->fetchInvoice($order->getId(), $context);
        $drop = $this->fetchService->fetchActiveDropFromOrder($order, $context);
        $documentConfig = $this->documentConfigLoader->load('invoice', $order->getSalesChannelId(), $context);

        $receiver = $drop ? $drop->getBank() : $documentConfig->bankName;
        $iban = $drop ? $drop->getIban() : $documentConfig->bankIban;
        $bic  = $drop ? $drop->getBic() : $documentConfig->bankBic;

        $paymentData = Data::create()
            ->setName($receiver)
            ->setIban($iban)
            ->setBic($bic)
            ->setAmount($order->getPrice()->getTotalPrice())
            ->setRemittanceText('Rechnungsnr. ' . $order->getOrderNumber());

        $qrDataUri = Builder::create()
            ->data((string)$paymentData)
            ->errorCorrectionLevel(new ErrorCorrectionLevelMedium())
            ->build()
            ->getDataUri();

        $event->getPage()->setExtensions([
            'sepaQr' => $qrDataUri,
            'drop' => $drop,
            'invoice' => $invoice
        ]);
    }

    private function fetchInvoice(string $orderId, Context $context) : ?DocumentEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('orderId', $orderId));
        $criteria->addFilter(new EqualsFilter('documentType.technicalName', 'invoice'));

        return $this->documentRepository->search($criteria, $context)->first();
    }
}