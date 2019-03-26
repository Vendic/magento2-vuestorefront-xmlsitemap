# VueStorefront XML sitemap generator for Magento 2
[![Latest Stable Version](https://poser.pugx.org/vendic/magento2-vuestorefront-xmlsitemap/v/stable)](https://packagist.org/packages/vendic/magento2-vuestorefront-xmlsitemap)
[![Latest Unstable Version](https://poser.pugx.org/vendic/magento2-vuestorefront-xmlsitemap/v/unstable)](https://packagist.org/packages/vendic/magento2-vuestorefront-xmlsitemap)
[![License](https://poser.pugx.org/vendic/magento2-vuestorefront-xmlsitemap/license)](https://packagist.org/packages/vendic/magento2-vuestorefront-xmlsitemap)
[![Build Status](https://travis-ci.org/Vendic/magento2-vuestorefront-xmlsitemap.svg?branch=master)](https://travis-ci.org/Vendic/magento2-vuestorefront-xmlsitemap)

**Note: this package isn't production ready. Please test thoroughly before using in production** 

This modules generates a sitemap xml (via cron job, everyday at 00:00) for VueStorefront projects that are integrated with Magento 2. VueStorefront uses a special url structure, based on Magento 2 data:

**Categories:**

https://vuestorefronturl.com/urlkey-id

**Products:**

https://vuestorefronturl.com/sku/urlkey

## Installation
```
composer require composer require vendic/magento2-vuestorefront-xmlsitemap
```

Go to system configuration:
> Stores > Configuration > Vendic > VueStorefront

And set your Vuestorefront homepage url.

### Contributors
[Tjitse Efde](https://vendic.nl)

### License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
