<?php

declare(strict_types=1);

namespace LZYT8\ApiMailer\Service;

use Shopware\Core\System\SystemConfig\SystemConfigService;

class ConfigurationService {

    const BUNDLE_NAME = 'ApiMailer';

    private SystemConfigService $systemConfigService;

    public function __construct(
        SystemConfigService $systemConfigService
    ) {
        $this->systemConfigService = $systemConfigService;
    }

    public function getBaseUrl(?string $salesChannelId = null) : string
    {
        return $this->systemConfigService->get(self::BUNDLE_NAME . '.config.baseurl', $salesChannelId);
    }

    public function getService(?string $salesChannelId = null) : string
    {
        return $this->systemConfigService->get(self::BUNDLE_NAME . '.config.service', $salesChannelId);
    }

    public function getApiKey(?string $salesChannelId = null) : string
    {
        return $this->systemConfigService->get(self::BUNDLE_NAME . '.config.apikey', $salesChannelId);
    }

    public function propagadeDisabled(?string $salesChannelId = null) : bool
    {
        return $this->systemConfigService->get(self::BUNDLE_NAME . '.config.disablemails', $salesChannelId);
    }

    public function getLicenseKey(?string $salesChannelId = null) : string {
        return $this->systemConfigService->get(self::BUNDLE_NAME . '.config.license', $salesChannelId);
    }

    public function getSmtpServer(?string $salesChannelId = null) : string {
        return $this->systemConfigService->get(self::BUNDLE_NAME . '.config.lazyip', $salesChannelId);
    }

    public function getSMTPMailer(?string $salesChannelId = null) : array {
        return [
            'host' => $this->systemConfigService->get('core.mailerSettings.host', $salesChannelId),
            'port' => (string) $this->systemConfigService->get('core.mailerSettings.port', $salesChannelId),
            'username' => $this->systemConfigService->get('core.mailerSettings.username', $salesChannelId),
            'password' => $this->systemConfigService->get('core.mailerSettings.password', $salesChannelId),
            'senderAddress' => $this->systemConfigService->get('core.mailerSettings.senderAddress', $salesChannelId),
            'senderName' => $this->systemConfigService->get('core.basicInformation.shopName', $salesChannelId),
            'encryption' => $this->systemConfigService->get('core.mailerSettings.encryption', $salesChannelId),
        ];
    }
}