<?php
declare(strict_types=1);

/**
 * @author tjitse (Vendic)
 * Created on 22/03/2019 18:07
 */

namespace Vendic\VueStorefrontSitemap\Model;

use SitemapPHP\Sitemap;

class SitemapFactory
{
    public function create(string $domain) : Sitemap
    {
        return new Sitemap($domain);
    }
}
