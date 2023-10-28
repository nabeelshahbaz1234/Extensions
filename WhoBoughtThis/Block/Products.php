<?php
declare(strict_types=1);

/**
 * NOTICE OF LICENSE
 * You may not sell, distribute, sub-license, rent, lease or lend complete or portion of software to anyone.
 *
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future.
 *
 * @package   RLTSquare_WhoBoughtThis
 * @copyright Copyright (c) 2022 RLTSquare (https://www.rltsquare.com)
 * @contacts  support@rltsquare.com
 * @license  See the LICENSE.md file in module root directory
 */

namespace RLTSquare\WhoBoughtThis\Block;

use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Helper\Output;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemsFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Zend_Db_Expr;

/**
 * Class Products
 * @package RLTSquare\WhoBoughtThis\Block
 */
class Products extends Template
{
    /**
     * @var ProductRepositoryInterface
     */
    protected ProductRepositoryInterface $productFactory;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var OrderItemsFactory
     */
    protected OrderItemsFactory $order;

    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $productCollection;

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory;

    /**
     * @var CategoryRepositoryInterface
     */
    protected CategoryRepositoryInterface $category;

    /**
     * @var Output
     */
    protected Output $helperCatalog;

    /**
     * @var ListProduct
     */
    protected ListProduct $listProduct;

    /**
     * Products constructor.
     * @param Context $context
     * @param ProductRepositoryInterface $productFactory
     * @param StoreManagerInterface $storeManager
     * @param OrderItemsFactory $order
     * @param CollectionFactory $productCollection
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param CategoryRepositoryInterface $category
     * @param Output $helperCatalog
     * @param ListProduct $listProduct
     * @param array $data
     */
    public function __construct(
        Context                                                         $context,
        ProductRepositoryInterface                                      $productFactory,
        StoreManagerInterface                                           $storeManager,
        OrderItemsFactory                                               $order,
        CollectionFactory                                               $productCollection,
        ScopeConfigInterface                                            $scopeConfig,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        CategoryRepositoryInterface                                     $category,
        Output                                                          $helperCatalog,
        ListProduct                                                     $listProduct,
        array                                                           $data = []
    ) {
        $this->productFactory = $productFactory;
        $this->storeManager = $storeManager;
        $this->order = $order;
        $this->productCollection = $productCollection;
        $this->scopeConfig = $scopeConfig;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->category = $category;
        $this->helperCatalog = $helperCatalog;
        $this->listProduct = $listProduct;
        parent::__construct($context, $data);
    }

    /**
     * @param $id
     * @return ProductInterface
     * @throws NoSuchEntityException
     */
    public function getLoadProduct($id): ProductInterface
    {
        return $this->productFactory->getById($id);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */

    /**
     * @param $id
     * @return CategoryInterface
     * @throws NoSuchEntityException
     */
    public function getCategory($id): CategoryInterface
    {
        return $this->category->get($id);
    }

    /**
     * @return ListProduct
     */
    public function getListProduct(): ListProduct
    {
        return $this->listProduct;
    }

    /**
     * @throws LocalizedException
     */
    public function getCollection()
    {
        $excludeOutOfStock = [1];
        if (true) {
            $excludeOutOfStock[] = $this->scopeConfig->getValue('whoBoughtThis/whoBoughtThisGroup/stock', ScopeInterface::SCOPE_STORE);   //get backend selected option exclude out of stock
        }
        $level = $this->getCategoryHierarchy();
        $current_product = $this->getCurrentProduct()->getId();
        $category_ids = $this->getCurrentProduct()->getCategoryIds();

        //Product is not assigned to any category so there will be no suggestions.
        if (empty($category_ids)) {
            return false;
        }

        $categories = $this->getCategoryCollection()->create();
        $categories->addAttributeToSelect('entity_id');
        $categories->addAttributeToSelect('parent_id');
        $categories->addAttributeToFilter('entity_id', $category_ids);
        $categories->load();

        $limit = $this->getDisplayLimit();
        foreach ($categories as $category) {
            $path = explode("/", $category->getPath());
            if (sizeof($path) > $level) {
                $i = sizeof($path) - $level;
            } else {
                $i = 0;
            }
            while ($i < (sizeof($path) - 1)) {
                $category_ids[] = $path[$i];
                $i++;
            }
        }

        // Retrieve all order_ids containing current product
        $currentProductOrders = $this->getOrderCollection()->create();
        $currentProductOrdersCollection = $currentProductOrders;
        $currentProductOrdersCollection->addAttributeToSelect('order_id');
        $currentProductOrdersCollection->addAttributeToSelect('product_id');
        $currentProductOrdersCollection->addFieldToFilter('product_id', $current_product);
        $currentProductOrders = $currentProductOrdersCollection->getColumnValues('order_id');

        if ($currentProductOrders) {
//             Retrieve all product_ids in above order_ids except current product
            $productOrders = $this->getOrderCollection()->create();
            $productOrdersCollection = $productOrders;
            $productOrdersCollection->addAttributeToSelect('order_id');
            $productOrdersCollection->addAttributeToSelect('product_id');
            $productOrdersCollection->addFieldToFilter('order_id', ['in' => $currentProductOrders]);
            $productOrdersCollection->addFieldToFilter('product_id', ['nin' => $current_product]);
            $productOrders = $productOrdersCollection->getColumnValues('product_id');

            if ($productOrders) {
                // Count the occurrence of same product_ids and sort them in such a way that highest frequency product will appear on top of the array.
                $productOrders = array_count_values($productOrders);
                arsort($productOrders);

                // Filter product collection based on above product_ids and category_ids
                $productCollection = $this->getProductCollection()->create();
                $productCollection->addAttributeToSelect('entity_id');
                $productCollection->addFieldToFilter('entity_id', ['in' => array_keys($productOrders)]);
                $productCollection->addCategoriesFilter(['in' => $category_ids]);

                // Keep the same order of products the way we filtered them
                $productCollection->getSelect()->order(new Zend_Db_Expr('FIELD(e.entity_id, ' . implode(',', array_keys($productOrders)) . ')'));

                // Retrieve top products
                $productCollection->setPageSize($limit);
                $productCollection->setCurPage(1);
                $productCollection->setFlag('has_stock_status_filter', true);
                $productCollection->joinField('qty', 'cataloginventory_stock_status', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1', 'left');
                $productCollection->joinTable('cataloginventory_stock_status', 'product_id=entity_id', ['stock_status' => 'stock_status']);
                $productCollection->addAttributeToSelect('stock_status');
                $productCollection->addFieldToFilter('stock_status', ['in' => $excludeOutOfStock]);
                $productCollection->addFieldToFilter('visibility', ['nin' => 1]);
                $productCollection->load();
                return $productCollection;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @return string
     */
    public function getCategoryHierarchy()
    {
        return $this->scopeConfig->getValue('whoBoughtThis/whoBoughtThisGroup/dropdown', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return Product
     */
    public function getCurrentProduct(): Product
    {
        return $this->listProduct->getProduct();
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    public function getCategoryCollection(): \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
    {
        return $this->categoryCollectionFactory;
    }

    /**
     * @return string
     */
    public function getDisplayLimit()
    {
        return $this->scopeConfig->getValue('whoBoughtThis/whoBoughtThisGroup/display_limit', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return OrderItemsFactory
     */
    public function getOrderCollection(): OrderItemsFactory
    {
        return $this->order;
    }

    /**
     * @return CollectionFactory
     */
    public function getProductCollection(): CollectionFactory
    {
        return $this->productCollection;
    }

    /**
     * @return Output
     */
    public function getCatalogHelper(): Output
    {
        return $this->helperCatalog;
    }

    /**
     * @param $productImage
     * @return string
     */
    public function getLazyLoadedImage($productImage): string
    {
        $imageElement = $productImage->toHtml();
        return str_replace('src', 'data-lazy', $imageElement);
    }

    /**
     * @return string
     */
    public function isEnableDisable()
    {
        return $this->scopeConfig->getValue('whoBoughtThis/whoBoughtThisGroup/isEnableDisable', ScopeInterface::SCOPE_STORE);
    }
}
