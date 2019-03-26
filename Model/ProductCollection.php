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

    public function __construct(
        Status $productStatus,
        CollectionFactory $productCollectionFactory,
        Visibility $productVisibility
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
    }

    public function get()
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('url_key');
        $collection->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()]);
        $collection->setVisibility($this->productVisibility->getVisibleInSiteIds());

        return $collection;
    }
}
