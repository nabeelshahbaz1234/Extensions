<?php
/**
 * NOTICE OF LICENSE
 * You may not sell, distribute, sub-license, rent, lease or lend complete or portion of software to anyone.
 *
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future.
 *
 * @package   RLTSquare_MostViewed
 * @copyright Copyright (c) 2022 RLTSquare (https://www.rltsquare.com)
 * @contacts  support@rltsquare.com
 * @license  See the LICENSE.md file in module root directory
 */
declare(strict_types=1);

namespace RLTSquare\MostViewed\Block;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Helper\Output;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Reports\Model\ResourceModel\Product\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Viewed
 * @package RLTSquare\MostViewed\Block
 */
class Viewed extends \Magento\Backend\Block\Dashboard\Tab\Products\Viewed
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
     * @var ListProduct
     */
    protected ListProduct $listProduct;

    /**
     * @var Output
     */
    protected Output $helperCatalog;

    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $productsFactory;

    /**
     * @var ScopeConfigInterface
     */
    public ScopeConfigInterface $scopeConfig;

    /**
     * @var Http
     */
    protected Http $request;

    /**
     * Viewed constructor.
     * @param Context $context
     * @param Data $backendHelper
     * @param CollectionFactory $productsFactory
     * @param ProductRepositoryInterface $productFactory
     * @param StoreManagerInterface $storeManager
     * @param ListProduct $listProduct
     * @param Output $helperCatalog
     * @param ScopeConfigInterface $scopeConfig
     * @param Http $request
     * @param array $data
     */
    public function __construct(
        Context                    $context,
        Data                       $backendHelper,
        CollectionFactory          $productsFactory,
        ProductRepositoryInterface $productFactory,
        StoreManagerInterface      $storeManager,
        ListProduct                $listProduct,
        Output                     $helperCatalog,
        ScopeConfigInterface       $scopeConfig,
        Http                       $request,
        array                      $data = []
    ) {
        parent::__construct($context, $backendHelper, $productsFactory, $data);
        $this->productFactory = $productFactory;
        $this->storeManager = $storeManager;
        $this->listProduct = $listProduct;
        $this->helperCatalog = $helperCatalog;
        $this->productsFactory = $productsFactory;
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
    }

    /**
     * @return \Magento\Backend\Block\Dashboard\Tab\Products\Viewed
     * @throws LocalizedException
     */
    protected function _prepareCollection(): \Magento\Backend\Block\Dashboard\Tab\Products\Viewed
    {
        if ($this->getParam('website')) {
            $storeIds = $this->storeManager->getWebsite($this->getParam('website'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } elseif ($this->getParam('group')) {
            $storeIds = $this->storeManager->getGroup($this->getParam('group'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } else {
            $storeId = (int)$this->getParam('store');
        }

        $excludeOutOfStock = [1];
        if (true) {
            $excludeOutOfStock[] = $this->scopeConfig->getValue('mostViewed/mostViewedGroup/stock', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);   //get backend selected option exclude out of stock
        }

        $collection = $this->productsFactory->create()->addAttributeToSelect('*');
        $collection->addViewsCount()->setStoreId($storeId);
        $collection->addStoreFilter($storeId);
        $collection->setPageSize($this->scopeConfig->getValue('mostViewed/mostViewedGroup/limit', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));   //Get backend selected option most viewed products
        $collection->setCurPage(1);
        $collection->setFlag('has_stock_status_filter', true);
        $collection->joinField('qty', 'cataloginventory_stock_status', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1', 'left');
        $collection->joinTable('cataloginventory_stock_status', 'product_id=entity_id', ['stock_status' => 'stock_status']);
        $collection->addAttributeToSelect('stock_status');
        $collection->addFieldToFilter('stock_status', ['in' => $excludeOutOfStock]);

        $this->setCollection($collection);

        /** @var Product $product */
        foreach ($collection as $product) {
            $product->setPrice($product->getPriceInfo()->getPrice(\Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE)->getValue());
        }
        return $this;
    }

    /**
     * @param $id
     * @return object
     * @throws NoSuchEntityException
     */
    public function getLoadProduct($id): object
    {
        return $this->productFactory->getById($id);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getBaseURLofStore(): string
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }

    /**
     * @return ListProduct
     */
    public function getListProduct(): ListProduct
    {
        return $this->listProduct;
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
     * @return int
     */
    public function isEnableDisable()
    {
        return $this->scopeConfig->getValue('mostViewed/mostViewedGroup/isEnableDisable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return array
     */
    public function getVisibility(): array
    {
        $map = [
            'Product Page' => 'catalog_product_view',
            'Category Page' => 'catalog_category_view',
            'Home Page' => 'cms_index_index'
        ];
        $visibilityValues = $this->scopeConfig->getValue('mostViewed/mostViewedGroup/visibility', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $visibility = explode(',', $visibilityValues);

        $matchValues = [];

        foreach ($visibility as $key => $value) {
            $matchValues[$value] = $map[$visibility[$key]];
        }
        return $matchValues;
    }

    /**
     * @return bool
     */
    public function getCurrentPagePath(): bool
    {
        $visible = $this->getVisibility();
        $fullActionName = $this->request->getFullActionName();
        return in_array($fullActionName, $visible);
    }
}
