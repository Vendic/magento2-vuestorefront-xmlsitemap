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
     * @magentoDataFixtureBeforeTransaction createFixture
     */
    public function testFileGeneration()
    {
        // Action
        $this->generateSitemap();

        // Get results
        $fileAbsolutePath = $this->getSitemapPath();
        $actualContent = file_get_contents($fileAbsolutePath);
        $expectedContent = $this->expectedSitemapContent();

        // Cleanup
        $this->removeFile($fileAbsolutePath);

        // Assertions
        $this->assertSame($expectedContent, $actualContent);
        $this->assertFalse(file_exists($fileAbsolutePath));
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

    public static function createFixtureRollback()
    {
        ProductFixtureRollback::create()->execute(self::$visibleProductFixture);
        ProductFixtureRollback::create()->execute(self::$invisibleProductFixture);
        CategoryFixtureRollback::create()->execute(self::$categoryFixture);
    }

    /**
     * @return string
     */
    private function expectedSitemapContent(): string
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
}
