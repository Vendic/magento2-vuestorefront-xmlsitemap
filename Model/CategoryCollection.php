<?php
declare(strict_types=1);

/**
 * @author tjitse (Vendic)
 * Created on 26/03/2019 09:43
 */

namespace Vendic\VueStorefrontSitemap\Model;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

class CategoryCollection
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    public function get()
    {
        $collection = $this->collectionFactory
        ->create()
        ->addFieldToSelect('url_key')
        ->addFieldToFilter('url_key', ['neq' => null])
        ->addFieldToSelect('url_path')
        ->addFieldToFilter('url_path', ['neq' => null])
        ->addFieldToSelect('is_active')
        ->addFieldToSelect('level')
        ->addFieldToFilter('is_active', ['eq' => 1])
        ->addFieldToFilter('level', ['gt' => 1]);

        return $collection;
    }
}
