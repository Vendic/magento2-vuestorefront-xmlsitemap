# VueStorefront XML sitemap generator for Magento 2
[![Latest Stable Version](https://poser.pugx.org/vendic/magento2-vuestorefront-xmlsitemap/v/stable)](https://packagist.org/packages/vendic/magento2-vuestorefront-xmlsitemap)
[![Latest Unstable Version](https://poser.pugx.org/vendic/magento2-vuestorefront-xmlsitemap/v/unstable)](https://packagist.org/packages/vendic/magento2-vuestorefront-xmlsitemap)
[![License](https://poser.pugx.org/vendic/magento2-vuestorefront-xmlsitemap/license)](https://packagist.org/packages/vendic/magento2-vuestorefront-xmlsitemap)

This modules generates a sitemap xml (via cron job, everyday at 00:00) for VueStorefront projects that are integrated with Magento 2. Also adds a cli command `bin/magento vsf:sitemap:generate` to manually generate a sitemap. VueStorefront uses a special url structure, based on Magento 2 data:

**Example category URL structure:**

https://vuestorefronturl.com/urlkey-id

**Example product URL structure:**

https://vuestorefronturl.com/sku/urlkey

**Example VueStorefront 1.9 category URL structure:**

https://vuestorefronturl.com/urlpath

**Example VueStorefront 1.9 product URL structure:**

https://vuestorefronturl.com/urlkey

## Support

Magento 2.2 | Magento 2.3
--- | :---:
:white_check_mark: | :white_check_mark:

## Installation
```
composer require vendic/magento2-vuestorefront-xmlsitemap
```

Go to system configuration:
> Stores > Configuration > Vendic > VueStorefront

And set your Vuestorefront homepage url.

### Contributors
[Tjitse Efde](https://vendic.nl)

### License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

### About Vendic
[Vendic - Magento 2](https://vendic.nl "Vendic Homepage") develops technically challenging e-commerce websites using Magento 2. Feel free to check out our projects on our website.
