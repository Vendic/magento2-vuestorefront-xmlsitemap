<?php
declare(strict_types=1);

/**
 * @author tjitse (Vendic)
 * Created on 23/03/2019 10:33
 */

namespace Vendic\VueStorefrontSitemap\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;

class Configuration
{
    const VUE_STOREFRONT_URL_CONFIG_PATH = 'vuestorefront/sitemap/vs_url';
    const VUE_STOREFRONT_SHORT_CATALOG_ENABLED = 'vuestorefront/sitemap/use_catalog_short_urls';
    const VUE_STOREFRONT_CATEGORY_ID_SUFFIX_ENABLED = 'vuestorefront/sitemap/category_id_suffix';

    const VUE_STOREFRONT_EXCLUDE_PRODUCT_SKUS_ENABLED = 'vuestorefront/sitemap/exclude_product_skus';
    const VUE_STOREFRONT_SITEMAP_FOLDER = 'vuestorefront/sitemap/sitemap_folder';
    const VUE_STOREFRONT_SITEMAP_FILE = 'vuestorefront/sitemap/sitemap_file';

    const VUE_STOREFRONT_CATEOGRY_URL_PATH_ENABLED = 'vuestorefront/sitemap/category_url_path';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function getVueStorefrontUrl(): string
    {
        $url = $this->scopeConfig->getValue(
            self::VUE_STOREFRONT_URL_CONFIG_PATH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (!is_string($url)) {
            throw new LocalizedException(
                __('Invalid or no VueStorefront url entered for config path %1', self::VUE_STOREFRONT_URL_CONFIG_PATH)
            );
        }
        return $url;
    }

    public function getShortCatalogUrlsEnabled(): bool
    {
        $setting = $this->scopeConfig->getValue(
            self::VUE_STOREFRONT_SHORT_CATALOG_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return (bool) $setting;
    }

    public function getCategoryIdSuffixEnabled(): bool
    {
        $setting = $this->scopeConfig->getValue(
            self::VUE_STOREFRONT_CATEGORY_ID_SUFFIX_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return (bool) $setting;
    }

    public function getExcludeProductSkusEnabled(): bool
    {
        $setting = $this->scopeConfig->getValue(
            self::VUE_STOREFRONT_EXCLUDE_PRODUCT_SKUS_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return (bool) $setting;
    }

    public function getVueStorefrontSitemapFolder(): string
    {
        $folder = '/' . $this->scopeConfig->getValue(
            self::VUE_STOREFRONT_SITEMAP_FOLDER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) . '/';
        if (!is_string($folder)) {
            $folder = '/';
        }
        return $folder;
    }

    public function getVueStorefrontSitemapFilename(): string
    {
        $file = $this->scopeConfig->getValue(self::VUE_STOREFRONT_SITEMAP_FILE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!is_string($file)) {
            $folder = 'sitemap';
        }
        return $file;
    }

    public function getVueStorefrontCategoryUrlPath(): bool
    {
        $setting = $this->scopeConfig->getValue(
            self::VUE_STOREFRONT_CATEOGRY_URL_PATH_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return (bool) $setting;
    }
}
