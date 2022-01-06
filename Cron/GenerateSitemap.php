<?php
declare(strict_types=1);

/**
 * @author tjitse (Vendic)
 * Created on 22/03/2019 15:31
 */

namespace Vendic\VueStorefrontSitemap\Cron;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use SitemapPHP\Sitemap;
use Vendic\VueStorefrontSitemap\Model\CategoryCollection;
use Vendic\VueStorefrontSitemap\Model\Configuration;
use Vendic\VueStorefrontSitemap\Model\ProductCollection;
use Vendic\VueStorefrontSitemap\Model\SitemapFactory;
use Magento\Framework\Filesystem\Driver\File;

class GenerateSitemap
{
    const SITEMAP_NAME = 'sitemap.xml';

    /**
     * @var DirectoryList
     */
    protected $directoryList;
    /**
     * @var SitemapFactory
     */
    protected $sitemapFactory;
    /**
     * @var ProductCollection
     */
    protected $productCollection;
    /**
     * @var Sitemap
     */
    protected $sitemap;
    /**
     * @var Configuration
     */
    protected $configuration;
    /**
     * @var CategoryCollection
     */
    protected $categoryCollection;
    /**
     * @var File
     */
    protected $fileDriver;

    public function __construct(
        CategoryCollection $categoryCollection,
        Configuration $configuration,
        ProductCollection $productCollection,
        DirectoryList $directoryList,
        SitemapFactory $sitemapFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $appConfigScopeConfig,
        File $fileDriver
    ) {
        $this->directoryList = $directoryList;
        $this->sitemapFactory = $sitemapFactory;
        $this->productCollection = $productCollection;
        $this->configuration = $configuration;
        $this->categoryCollection = $categoryCollection;
        $this->fileDriver = $fileDriver;
        $this->getAppConfigScopeConfig = $appConfigScopeConfig;
    }

    public function execute() : void
    {
        // Collect settings
        $domain = $this->configuration->getVueStorefrontUrl();
        $path = $this->getPubPath();

        // Create directory at Path if doesn't exists
        if (!$this->fileDriver->isDirectory($path)) $this->fileDriver->createDirectory($path, 0775);
        
        // Sitemap configuration
        $this->sitemap = $this->sitemapFactory->create($domain);
        $this->sitemap->setPath($path);
        $this->sitemap->setFilename('sitemap');

        // Add data
        $this->addHomepageToSitemap();
        $this->addCategoriesToSitemap();
        $this->addProductsToSitemap();

        // Generate
        $this->sitemap->createSitemapIndex($domain . "/", 'Today');
    }

    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function getPubPath() : string
    {
        return $this->directoryList->getPath('pub') . $this->configuration->getVueStorefrontSitemapFolder();
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    private function getActiveProducts()
    {
        return $this->productCollection->get();
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    private function getActiveCategories()
    {
        return $this->categoryCollection->get();
    }

    protected function addProductsToSitemap(): void
    {
        $activeProducts = $this->getActiveProducts();
        $productUrlSuffix = $this->getAppConfigScopeConfig->getValue('catalog/seo/product_url_suffix');
        if ($activeProducts->count() >= 1) {
            /** @var ProductInterface $product */
            foreach ($activeProducts->getItems() as $product) {
                $productUrl = $this->generateProductSitemapUrl($product) . $productUrlSuffix;
                $this->sitemap->addItem(
                    $productUrl,
                    1.0,
                    'daily',
                    $product->getUpdatedAt()
                );
            }
        }
    }

    protected function addCategoriesToSitemap(): void
    {
        $activeCategories = $this->getActiveCategories();
        $categoryUrlSuffix = $this->getAppConfigScopeConfig->getValue('catalog/seo/category_url_suffix');
        if ($activeCategories->count() >= 1) {
            /** @var CategoryInterface $category */
            foreach ($activeCategories->getItems() as $category) {
                $categoryUrl = $this->generateSitemapCategoryUrl($category) . $categoryUrlSuffix;
                $this->sitemap->addItem(
                    $categoryUrl,
                    1.0,
                    'daily',
                    $category->getUpdatedAt()
                );
            }
        }
    }

    protected function addHomepageToSitemap() : void
    {
        $this->sitemap->addItem(
            '/'
        );
    }

    /**
     * @param $product
     * @return string
     */
    protected function generateProductSitemapUrl(ProductInterface $product): string
    {
        $prefix = '';
        if (!$this->configuration->getShortCatalogUrlsEnabled()) {
            $prefix = 'p/';
        }
        if (!$this->configuration->getExcludeProductSkusEnabled()) {
            $url = '/' . $prefix . $product->getSku() . '/' . $product->getUrlKey();
        } else {
            $url = '/' . $prefix . $product->getUrlKey();
        }
        return $url;
    }

    /**
     * @param $category
     * @return string
     */
    protected function generateSitemapCategoryUrl(CategoryInterface $category): string
    {
        $prefix = '';
        if (!$this->configuration->getShortCatalogUrlsEnabled()) {
            $prefix = 'c/';
        }
        if ($this->configuration->getVueStorefrontCategoryUrlPath()) {
            $url = '/' . $prefix . $category->getUrlPath();
        } else {
            $url = '/' . $prefix . $category->getUrlKey();
        }
        if ($this->configuration->getCategoryIdSuffixEnabled()) {
            $url = $url . '-' . $category->getId();
        }

        return $url;
    }
}
