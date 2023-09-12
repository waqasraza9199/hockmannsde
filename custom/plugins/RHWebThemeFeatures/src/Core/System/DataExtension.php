<?php declare(strict_types=1);

namespace RHWeb\ThemeFeatures\Core\System;

class DataExtension
{
    private ?array $globalReplacers = null;

    /**
     * @param string $key
     * @param string|null $fallback
     * @return string|null
     */
    public function getReplacer(string $key, ?string $fallback = null): ?string
    {
        $key = sprintf("{%s}", strtoupper($key));

        return isset($this->globalReplacers[$key]) ? $this->globalReplacers[$key] : $fallback;
    }

    /**
     * @return bool
     */
    public function customerRequired(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isCleanUp(): bool
    {
        return true;
    }

    /**
     * @return array|null
     */
    public function getGlobalReplacers(): ?array
    {
        return $this->globalReplacers;
    }

    /**
     * @param array|null $globalReplacers
     */
    public function setGlobalReplacers(?array $globalReplacers): void
    {
        $this->globalReplacers = $globalReplacers;
    }

    public function process(): void
    {
    }

    public function getRemoveQueries(): array
    {
        return [];
    }

    public function getPreInstallQueries(): array
    {
        return [];
    }

    public function getInstallQueries(): array
    {
        return [];
    }

    public function getInstallConfig(): array
    {
        return [];
    }

    public function getStylesheets(): array
    {
        return [];
    }

    public function getTables(): ?array
    {
        return array_merge(
            $this->getShopwareTables(),
            $this->getPluginTables()
        );
    }

    public function getShopwareTables(): ?array
    {
        return [
            'seo_url_template',
            'media_folder',
            'product_manufacturer',
            'cms_page',
            'cms_page_translation',
            'cms_section',
            'cms_block',
            'cms_slot',
            'category',
            'category_translation',
            'product',
            'product_translation',
            'product_category',
            'product_visibility',
            'product_stream',
            'product_cross_selling',
            'custom_field_set',
            'mail_template_type',
            'mail_template_type_translation',
            'mail_template',
            'mail_template_translation',
            'event_action',
            'theme',
            'theme_sales_channel',
            'sales_channel',
            'payment_method',
            'shipping_method',
            "sales_channel_payment_method",
            "sales_channel_shipping_method"
        ];
    }

    public function getName(): string
    {
        return 'standard';
    }

    public function getType(): string
    {
        return 'demo';
    }

    public function getPluginName(): string
    {
        return 'RHWebThemeFeatures';
    }

    public function getCreatedAt(): string
    {
        return '2021-11-11 00:00:00.000';
    }

    public function getPluginTables(): ?array
    {
        return [];
    }

    /**
     * @deprecated tag:v1.4.16 Use {ID:XYZ123} in your JSON File instead
     */
    public function getDemoPlaceholderTypes(): array
    {
        return [
            'CATEGORY',
            'PRODUCT',
            'CMS_PAGE',
            'CMS_SECTION',
            'CMS_BLOCK',
            'CMS_SLOT',
            'WILD'
        ];
    }

    /**
     * @deprecated tag:v1.4.11 Will be deleted. Use {MEDIA_FILE:path/to/file.jpg} in Future
     */
    public function getMediaProperties(): array
    {
        return [
            [
                'table' => null,
                'mediaFolder' => null,
                'properties' => [
                    'mediaId',
                    'previewMediaId'
                ]
            ]
        ];
    }

    /**
     * @deprecated tag:v1.4.16 Use {ID:XYZ123} in your JSON File instead
     */
    public function getDemoPlaceholderCount(): int
    {
        return 500;
    }

    public function getLocalReplacers(): array
    {
        return [];
    }
}
