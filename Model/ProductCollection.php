<?php
declare(strict_types=1);

/**
 * @author tjitse (Vendic)
 * Created on 22/03/2019 18:39
 */

namespace Vendic\VueStorefrontSitemap\Model;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class ProductCollection
{

    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;
    /**
     * @var Status
     */
    protected $productStatus;
    /**
     * @var Visibility
     */
    protected $productVisibility;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        StoreManagerInterface $storeManager,
        Status $productStatus,
        CollectionFactory $productCollectionFactory,
        Visibility $productVisibility
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
        $this->storeManager = $storeManager;
    }

    /**
     * Get active products
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function get()
    {
        $collection = $this->productCollectionFactory->create();
        // We need to add store filters first to avoid an Zend_Db_Statement_Exception https://github.com/magento/magento2/issues/15187
        $collection->addStoreFilter($this->getDefaultStoreId());
        $collection->addAttributeToSelect('url_key');
        $collection->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()]);
        $collection->setVisibility($this->productVisibility->getVisibleInSiteIds());

        return $collection;
    }

    /**
     * TODO make store ID configurable via Magento backend
     * @return int
     */
    private function getDefaultStoreId()
    {
        return $this->storeManager->getDefaultStoreView()->getId();
    }
}
