<?php
namespace Lenz\GoogleShopping\Subscriber;

use Shopware\Core\Content\ProductExport\Event\ProductExportChangeEncodingEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductExportChangeEncodingEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            ProductExportChangeEncodingEvent::class => 'onProductExportChangeEncodingEvent',
        ];
    }

    public function onProductExportChangeEncodingEvent(ProductExportChangeEncodingEvent $event) {
        if(strtolower($event->getProductExportEntity()->getEncoding()) !== 'utf-8') {
            return;
        }

        if(strtolower($event->getProductExportEntity()->getFileFormat()) !== 'xml') {
            return;
        }

        $event->setEncodedContent($this->utf8_for_xml($event->getEncodedContent()));
    }

    private function utf8_for_xml($string)
    {
        // Replace not allowed unicode representations.
        $string = str_replace('&#56256;', '', $string);
        $string = str_replace('&#56496;', '', $string);

        // Replace unicode whitespaces with normal whitespace.
        $string =  preg_replace ('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $string);

        return $string;
    }
}
