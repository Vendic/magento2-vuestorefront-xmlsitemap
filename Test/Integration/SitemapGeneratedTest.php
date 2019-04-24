<?php
declare(strict_types=1);

/**
 * @author tjitse (Vendic)
 * Created on 22/03/2019 15:45
 */

namespace Vendic\VueStorefrontSitemap\Test\Integration;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;
use TddWizard\Fixtures\Catalog\CategoryBuilder;
use TddWizard\Fixtures\Catalog\CategoryFixture;
use TddWizard\Fixtures\Catalog\CategoryFixtureRollback;
use TddWizard\Fixtures\Catalog\ProductBuilder;
use TddWizard\Fixtures\Catalog\ProductFixture;
use TddWizard\Fixtures\Catalog\ProductFixtureRollback;
use Vendic\VueStorefrontSitemap\Cron\GenerateSitemap;

/**
 * @magentoDbIsolation enabled
 * @magentoAppArea frontend
 * @magentoAppIsolation enabled
 */
final class SitemapGeneratedTest extends TestCase
{
    /**
     * @var ProductFixture
     */
    private static $visibleProductFixture;
    /**
     * @var ProductFixture
     */
    private static $invisibleProductFixture;
    /**
     * @var CategoryFixture
     */
    private static $categoryFixture;
    /**
     * @var ObjectManager
     */
    private $objectManager;
    /**
     * @var GenerateSitemap
     */
    private $generateSitemapCron;
    /**
     * @var DirectoryList
     */
    private $directoryList;
    /**
     * @var File
     */
    private $fileSystem;

    public function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->generateSitemapCron = $this->objectManager->get(GenerateSitemap::class);
        $this->directoryList = $this->objectManager->get(DirectoryList::class);
        $this->fileSystem = $this->objectManager->get(File::class);
    }

    /**
     * @magentoConfigFixture current_store vuestorefront/sitemap/vs_url https://www.vendic.nl
     * @magentoConfigFixture current_store vuestorefront/sitemap/use_catalog_short_urls 0
     * @magentoConfigFixture current_store vuestorefront/sitemap/category_id_suffix 0
     * @magentoDataFixtureBeforeTransaction createFixture
     */
    public function testFileGenerationsWithShortCatalogUrlsDisabled()
    {
        // Action
        $this->generateSitemap();

        // Get results
        $fileAbsolutePath = $this->getSitemapPath();
        $actualContent = file_get_contents($fileAbsolutePath);
        $expectedContent = $this->expectedSitemapContentLongUrls();

        $this->assertSameAndRemoveFile($fileAbsolutePath, $expectedContent, $actualContent);
    }

    /**
     * @magentoConfigFixture current_store vuestorefront/sitemap/vs_url https://www.vendic.nl
     * @magentoConfigFixture current_store vuestorefront/sitemap/use_catalog_short_urls 1
     * @magentoConfigFixture current_store vuestorefront/sitemap/category_id_suffix 0
     * @magentoDataFixtureBeforeTransaction createFixture
     */
    public function testFileGenerationsWithShortCatalogUrlsEnabled()
    {
        // Action
        $this->generateSitemap();

        // Get results
        $fileAbsolutePath = $this->getSitemapPath();
        $actualContent = file_get_contents($fileAbsolutePath);
        $expectedContent = $this->expectedSitemapContentShortUrls();

        $this->assertSameAndRemoveFile($fileAbsolutePath, $expectedContent, $actualContent);
    }

    /**
     * @magentoConfigFixture current_store vuestorefront/sitemap/vs_url https://www.vendic.nl
     * @magentoConfigFixture current_store vuestorefront/sitemap/use_catalog_short_urls 0
     * @magentoConfigFixture current_store vuestorefront/sitemap/category_id_suffix 1
     * @magentoDataFixtureBeforeTransaction createFixture
     */
    public function testFileGenerationWithCategoryIdSuffix()
    {
        // Action
        $this->generateSitemap();

        // Get results
        $fileAbsolutePath = $this->getSitemapPath();
        $actualContent = file_get_contents($fileAbsolutePath);
        $expectedContent = $this->expectedSitemapContentWithSuffixEnabled();

        $this->assertSameAndRemoveFile($fileAbsolutePath, $expectedContent, $actualContent);
    }

    private function generateSitemap(): void
    {
        $this->generateSitemapCron->execute();
    }

    private function getSitemapPath(): string
    {
        $path = $this->directoryList->getPath('pub');
        return $path . '/' . GenerateSitemap::SITEMAP_NAME;
    }

    private function removeFile(string $filePath): void
    {
        unlink($filePath);
    }

    public static function createFixture()
    {
        self::$visibleProductFixture = new ProductFixture(
            ProductBuilder::aSimpleProduct()
                ->withSku('TEST123')
                ->withVisibility(4)
                ->withCustomAttributes(
                    [
                        ['url_key' => 'test-url-key']
                    ]
                )
                ->build()
        );
        self::$invisibleProductFixture = new ProductFixture(
            ProductBuilder::aSimpleProduct()
                ->withSku('invisible-product')
                ->withVisibility(1)
                ->build()
        );
        self::$categoryFixture = new CategoryFixture(
            CategoryBuilder::topLevelCategory()
                ->withIsActive(true)
                ->withName('Magento')
                ->withUrlKey('test-category')
                ->build()
        );
    }

    /**
     * @param $fileAbsolutePath
     * @param $expectedContent
     * @param $actualContent
     */
    protected function assertSameAndRemoveFile($fileAbsolutePath, $expectedContent, $actualContent): void
    {
        $this->removeFile($fileAbsolutePath);
        $this->assertSame($expectedContent, $actualContent);
        $this->assertFalse(file_exists($fileAbsolutePath));
    }

    public static function createFixtureRollback()
    {
        ProductFixtureRollback::create()->execute(self::$visibleProductFixture);
        ProductFixtureRollback::create()->execute(self::$invisibleProductFixture);
        CategoryFixtureRollback::create()->execute(self::$categoryFixture);
    }

    /**
     * @return string
     */
    private function expectedSitemapContentLongUrls(): string
    {
        $today = date('Y-m-d');

        return
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
 <url>
  <loc>https://www.vendic.nl/</loc>
  <priority>0.5</priority>
 </url>
 <url>
  <loc>https://www.vendic.nl/c/test-category</loc>
  <priority>1</priority>
  <changefreq>daily</changefreq>
  <lastmod>$today</lastmod>
 </url>
 <url>
  <loc>https://www.vendic.nl/p/TEST123/test123</loc>
  <priority>1</priority>
  <changefreq>daily</changefreq>
  <lastmod>$today</lastmod>
 </url>
</urlset>

XML;
    }

    /**
     * @return string
     */
    private function expectedSitemapContentShortUrls(): string
    {
        $today = date('Y-m-d');

        return
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
 <url>
  <loc>https://www.vendic.nl/</loc>
  <priority>0.5</priority>
 </url>
 <url>
  <loc>https://www.vendic.nl/test-category</loc>
  <priority>1</priority>
  <changefreq>daily</changefreq>
  <lastmod>$today</lastmod>
 </url>
 <url>
  <loc>https://www.vendic.nl/TEST123/test123</loc>
  <priority>1</priority>
  <changefreq>daily</changefreq>
  <lastmod>$today</lastmod>
 </url>
</urlset>

XML;
    }


    private function expectedSitemapContentWithSuffixEnabled()
    {
        $today = date('Y-m-d');
        $categoryId = self::$categoryFixture->getId();

        return
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
 <url>
  <loc>https://www.vendic.nl/</loc>
  <priority>0.5</priority>
 </url>
 <url>
  <loc>https://www.vendic.nl/c/test-category-$categoryId</loc>
  <priority>1</priority>
  <changefreq>daily</changefreq>
  <lastmod>$today</lastmod>
 </url>
 <url>
  <loc>https://www.vendic.nl/p/TEST123/test123</loc>
  <priority>1</priority>
  <changefreq>daily</changefreq>
  <lastmod>$today</lastmod>
 </url>
</urlset>

XML;
    }
}
