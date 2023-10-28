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
 * @package   RLTSquare_RecentlyViewed
 * @copyright Copyright (c) 2022 RLTSquare (https://www.rltsquare.com)
 * @contacts  support@rltsquare.com
 * @license  See the LICENSE.md file in module root directory
 */

namespace RLTSquare\RecentlyViewed\Block;

use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Helper\Output;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use RLTSquare\RecentlyViewed\Model\History;
use RLTSquare\RecentlyViewed\Model\ResourceModel\History\CollectionFactory;

/**
 * Class Viewed
 * @package RLTSquare\RecentlyViewed\Block
 */
class Viewed extends \Magento\Framework\View\Element\Template
{
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $recentlyViewedFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected ProductRepositoryInterface $productLoader;

    /**
     * @var Output
     */
    protected Output $helperCatalog;

    /**
     * @var FormKey
     */
    protected FormKey $formKey;

    /**
     * @var Http
     */
    protected Http $request;

    /**
     * @var Session
     */
    protected Session $customerSession;

    /**
     * @var History
     */
    protected History $history;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory;

    protected \Magento\Framework\App\Http\Context $httpContext;

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @var ListProduct
     */
    protected ListProduct $listProduct;
    private Context $context;
    private \Magento\Customer\Model\SessionFactory $customer;
    private array $data;

    /**
     * Viewed constructor.
     * @param Context $context
     * @param CollectionFactory $recentlyViewedFactory
     * @param ProductRepositoryInterface $productLoader
     * @param Output $helperCatalog
     * @param FormKey $formKey
     * @param Http $request
     * @param Session $customerSession
     * @param History $history
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param ScopeConfigInterface $scopeConfig
     * @param ListProduct $listProduct
     * @param array $data
     */
    public function __construct(
        Context                                                        $context,
        CollectionFactory                                              $recentlyViewedFactory,
        ProductRepositoryInterface                                     $productLoader,
        Output                                                         $helperCatalog,
        FormKey                                                        $formKey,
        Http                                                           $request,
        Session                                                        $customerSession,
        History                                                        $history,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\App\Http\Context                            $httpContext,
        ScopeConfigInterface                                           $scopeConfig,
        ListProduct                                                    $listProduct,
        \Magento\Customer\Model\SessionFactory $customer,
        array                                                          $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->recentlyViewedFactory = $recentlyViewedFactory;
        $this->productLoader = $productLoader;
        $this->helperCatalog = $helperCatalog;
        $this->formKey = $formKey;
        $this->request = $request;
        $this->history = $history;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->httpContext = $httpContext;
        $this->scopeConfig = $scopeConfig;
        $this->listProduct = $listProduct;
        parent::__construct($context, $data);
        $this->context = $context;
        $this->customer = $customer;
        $this->data = $data;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities(): array
    {
        $identities = [];
        $collection = $this->getCollection();
        if ($collection) {
            $ids = [];
            foreach ($collection as $item) {
                $ids[] = $item->getProductId();
            }
            $prodCollection = $this->productCollectionFactory->create()->addAttributeToSelect('*')
                ->addFieldToFilter('entity_id', ['in' => $ids])
                ->load();
            foreach ($prodCollection as $item) {
                $identities = array_merge($identities, $item->getIdentities());
            }
        }
        return $identities;
    }

    /**
     * @return ListProduct
     */
    public function getListProduct(): ListProduct
    {
        return $this->listProduct;
    }

    public function getCustomerIsLoggedIn(): bool
    {
        return (bool)$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    public function getCollection()
    {
        $historyCollection = null;
        $productDisplayLimit = $this->scopeConfig->getValue('recentlyViewed/recentlyViewedGroup/display_limit', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($this->getCustomerIsLoggedIn()) {
            // customer is logged in
            $customerId = $this->customer->create()->getCustomer()->getId();
            $historyCollection = $this->recentlyViewedFactory->create();
            $historyCollection->setPageSize($productDisplayLimit);
            $historyCollection->setCurPage(1);
            $historyCollection->addFieldToFilter('customer_id', $customerId);
        }
        return $historyCollection;
    }

    /**
     * @param $id
     * @return object
     * @throws NoSuchEntityException
     */
    public function getLoadProduct($id): object
    {
        return $this->productLoader->getById($id);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getBaseURLofStore(): string
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * @return Output
     */
    public function getCatalogHelper(): Output
    {
        return $this->helperCatalog;
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getFormKey(): string
    {
        return $this->formKey->getFormKey();
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
        return $this->_scopeConfig->getValue('recentlyViewed/recentlyViewedGroup/isEnableDisable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
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
        $visibilityValues = $this->_scopeConfig->getValue('recentlyViewed/recentlyViewedGroup/visibility', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $visibility = explode(',', $visibilityValues);

        $matchValues = [];

        foreach ($visibility as $key => $value) {
            $matchValues[$value] = $map[$visibility[$key]];
        }
        return $matchValues;
    }

    /**
     * @return string
     */
    public function getCurrentPageURL(): string
    {
        return $this->request->getFullActionName();
    }

    /**
     * @return bool
     */
    public function getCurrentPagePath(): bool
    {
        $visible = $this->getVisibility();
        $fullActionName = $this->getData(0);
        return in_array($fullActionName, $visible);
    }

    /**
     * @return bool
     */
    public function customerLoggedIn(): bool
    {
        return $this->customerSession->isLoggedIn();
    }
}
