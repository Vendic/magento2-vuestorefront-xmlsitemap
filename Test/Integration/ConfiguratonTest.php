<?php
declare(strict_types=1);

/**
 * @author tjitse (Vendic)
 * Created on 23/03/2019 10:42
 */

namespace Vendic\VueStorefrontSitemap\Test\Integration;

use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;
use Vendic\VueStorefrontSitemap\Model\Configuration;

final class ConfiguratonTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;
    /**
     * @var Configuration
     */
    private $configuration;

    public function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->configuration = $this->objectManager->get(Configuration::class);
    }

    /**
     * @magentoConfigFixture current_store vuestorefront/sitemap/vs_url https://www.vendic.nl
     */
    public function testGetVueStorefrontUrl()
    {
        $this->assertEquals('https://www.vendic.nl', $this->configuration->getVueStorefrontUrl());
    }

    public function testInvalidVueStorefrontUrl()
    {
        $this->expectException(LocalizedException::class);
        $this->configuration->getVueStorefrontUrl();
    }
}
