<?php

declare(strict_types=1);

namespace RHWeb\ThemeFeatures\Subscriber;

use Shopware\Storefront\Event\ThemeCompilerEnrichScssVariablesEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class ThemeVariableSubscriber implements EventSubscriberInterface
{
    protected $systemConfig;

    public function __construct(SystemConfigService $systemConfig)
    {
        $this->systemConfig = $systemConfig;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ThemeCompilerEnrichScssVariablesEvent::class => 'onAddVariables'
        ];
    }

    public function onAddVariables(ThemeCompilerEnrichScssVariablesEvent $event)
    {
        /** @var string $configExampleField */
        $configExampleField = $this->systemConfig->get('ScssPlugin.config.exampleColor', $event->getSalesChannelId());

        // pass the value from `exampleColor` to `addVariable`
        $event->addVariable('sass-plugin-example-color', $configExampleField);
    }
}

