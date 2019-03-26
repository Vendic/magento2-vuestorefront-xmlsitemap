<?php
declare(strict_types=1);

/**
 * @author tjitse (Vendic)
 * Created on 22/03/2019 19:06
 */

namespace Vendic\VueStorefrontSitemap\Test\Integration;

use Magento\Cron\Model\Config;
use Magento\TestFramework\Helper\Bootstrap;

final class CronJobTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;
    /**
     * @var Config
     */
    private $cronConfig;

    public function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->cronConfig = $this->objectManager->get(Config::class);
    }

    public function testIfCronIsInJobs()
    {
        $jobs = $this->cronConfig->getJobs();
        $expectedName = $jobs['default']['vs_sitemap_generation']['name'];
        $this->assertEquals($expectedName, 'vs_sitemap_generation');
    }
}
