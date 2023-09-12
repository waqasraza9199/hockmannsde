<?php declare(strict_types=1);

namespace RHWeb\ThemeFeatures\Core\Service;

use Shopware\Storefront\Framework\Cookie\CookieProviderInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class CustomCookieProvider implements CookieProviderInterface {

    private CookieProviderInterface $originalService;
    private SystemConfigService $systemConfigService;

    public function __construct(CookieProviderInterface $service, SystemConfigService $systemConfigService)
    {
        $this->originalService = $service;
        $this->systemConfigService = $systemConfigService;
    }

    private const googleFonts = [
        'snippet_name' => 'rhwebThemeFeatures.cookie.googleFonts.title',
        'snippet_description' => 'rhwebThemeFeatures.cookie.googleFonts.description',
        'cookie' => 'google-fonts',
        'value' => '1',
        'expiration' => '30'
    ];

    public function getCookieGroups(): array
    {
        $activateGoogleFonts = $this->systemConfigService->get('RHWebThemeFeatures.config.rhwebActivateGoogleFonts');
        $activateGoogleFontsCookie = $this->systemConfigService->get('RHWebThemeFeatures.config.rhwebActivateGoogleFontsCookie');

        if($activateGoogleFonts && $activateGoogleFontsCookie){
            return array_merge(
                $this->originalService->getCookieGroups(),
                [
                    self::googleFonts
                ]
            );
        }
        else{
            return $this->originalService->getCookieGroups();
        }
    }
}