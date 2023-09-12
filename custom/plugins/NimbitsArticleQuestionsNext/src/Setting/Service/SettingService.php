<?php declare(strict_types=1);
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nimbits\NimbitsArticleQuestionsNext\Setting\Service;

use Shopware\Core\Framework\Struct\StructCollection;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class SettingService
{

    public const SYSTEM_CONFIG_DOMAIN = 'NimbitsArticleQuestionsNext.config.';

    /**
     * @var SystemConfigService
     */
    private $systemConfigService;

    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->systemConfigService = $systemConfigService;
    }

    public function getSettingsAsStruct(?string $salesChannelId = null): StructCollection
    {
        $settings = $this->getSettingsAsArray($salesChannelId);

        return (new StructCollection())->assign(
            $settings
        );
    }

    public function getSettingsAsArray(?string $salesChannelId = null): array
    {
        $values = $this->systemConfigService->getDomain(
            self::SYSTEM_CONFIG_DOMAIN,
            $salesChannelId,
            true
        );

        $indexedValues = [];

        foreach ($values as $key => $value) {
            $property = substr($key, strlen(self::SYSTEM_CONFIG_DOMAIN));
            $indexedValues[$property] = $value;
        }

        return $indexedValues;
    }
}
