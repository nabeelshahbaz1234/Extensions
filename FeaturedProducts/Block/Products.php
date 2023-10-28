<?php
/**
 * NOTICE OF LICENSE
 * You may not sell, distribute, sub-license, rent, lease or lend complete or portion of software to anyone.
 *
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future.
 *
 * @package   RLTSquare_FeaturedProducts
 * @copyright Copyright (c) 2022 RLTSquare (https://www.rltsquare.com)
 * @contacts  support@rltsquare.com
 * @license  See the LICENSE.md file in module root directory
 */

namespace RLTSquare\FeaturedProducts\Block;

use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Helper\Output;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
/**
 * Class Products
 * @package RLTSquare\FeaturedProducts\Block
 */
class Products extends \Magento\Framework\View\Element\Template
{
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $productCollectionFactory;

    /**
     * @var Visibility
     */
    protected Visibility $productVisibility;

    /**
     * @var ListProduct
     */
    protected ListProduct $listProduct;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var Output
     */
    protected Output $helperCatalog;

    /**
     * @var ProductFactory
     */
    protected ProductFactory $productLoader;

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @var Product
     */
    protected Product $product;

    /**
     * @var Http
     */
    protected Http $request;

    /**
     * @var StockRegistryInterface
     */
    protected StockRegistryInterface $stockRegistry;

    /**
     * @var StockItemRepository
     */
    protected StockItemRepository $stockItemRepository;

    /**
     * Products constructor.
     * @param Context $context
     * @param CollectionFactory $productCollectionFactory
     * @param Visibility $productVisibility
     * @param ListProduct $listProduct
     * @param ProductFactory $productLoader
     * @param StoreManagerInterface $storeManager
     * @param Output $helperCatalog
     * @param ScopeConfigInterface $scopeConfig
     * @param Product $product
     * @param Http $request
     * @param StockItemRepository $stockItemRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $productCollectionFactory,
        Visibility $productVisibility,
        ListProduct $listProduct,
        ProductFactory $productLoader,
        StoreManagerInterface $storeManager,
        Output $helperCatalog,
        ScopeConfigInterface $scopeConfig,
        Product $product,
        Http $request,
        StockItemRepository $stockItemRepository,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productVisibility = $productVisibility;
        $this->productLoader = $productLoader;
        $this->storeManager = $storeManager;
        $this->listProduct = $listProduct;
        $this->helperCatalog = $helperCatalog;
        $this->scopeConfig = $scopeConfig;
        $this->product = $product;
        $this->request = $request;
        $this->stockItemRepository = $stockItemRepository;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getProductCollection()
    {
        $productLimit = $this->scopeConfig->getValue('featuredProducts/featuredProductsGroup/display_limit', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addWebsiteFilter();
        $collection->addStoreFilter();
        $collection->setVisibility($this->productVisibility->getVisibleInSiteIds());
        $collection->setPageSize($productLimit);
        $collection->addAttributeToFilter('featured_products',1);
        return $collection;
    }

    /**
     * @return ListProduct
     */
    public function getListProduct(): ListProduct
    {
        return $this->listProduct;
    }

    /**
     * @param $id
     * @throws NoSuchEntityException
     */
    protected function stockStatus($id)
    {
        if($this->scopeConfig->getValue('featuredProducts/featuredProductsGroup/stock', \Magento\Store\Model\ScopeInterface::SCOPE_STORE))
        {
            return $this->stockItemRepository->get($id);
        }
        else
            return true;
    }

    /**
     * @param $id
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getLoadProduct($id)
    {
        if($this->stockStatus($id))
            return $this->productLoader->create()->load($id);
        else
            return null;
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getBaseURLofStore(){
        return $this->storeManager->getStore()->getBaseUrl();
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
     * @return mixed
     */
    public function getLazyLoadedImage($productImage){
        $imageElement = $productImage->toHtml();
        return str_replace('src', 'data-lazy', $imageElement);
    }

    /**
     * @return int
     */
    public function isEnableDisable():int
    {
        return $this->scopeConfig->getValue('featuredProducts/featuredProductsGroup/isEnableDisable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return array
     */
    public function getVisibility(): array
    {
        $map = [
            '' => '',
            'Product Page' => 'catalog_product_view',
            'Category Page' => 'catalog_category_view',
            'Home Page' => 'cms_index_index'
        ];
        $visibilityValues = $this->scopeConfig->getValue('featuredProducts/featuredProductsGroup/visibility', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $visibility = explode(',', $visibilityValues);

        $matchValues = [];

        foreach ($visibility as $key => $value)
        {
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
        return in_array($fullActionName,$visible);
    }
}
