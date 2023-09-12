<?php

declare(strict_types=1);

namespace LZYT8\BetterInvoice\Service;

use Shopware\Core\System\SystemConfig\SystemConfigService;

class ConfigurationService {

    const BUNDLE_NAME = 'BetterInvoice';

    private SystemConfigService $systemConfigService;

    public function __construct(
        SystemConfigService $systemConfigService
    ) {
        $this->systemConfigService = $systemConfigService;
    }

    public function getLicenseKey(?string $salesChannelId = null) : string {
        return $this->systemConfigService->get(self::BUNDLE_NAME . '.config.license', $salesChannelId);
    }
}