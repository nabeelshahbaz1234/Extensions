<?php
declare(strict_types=1);
/**
 * author:awais.rehman@rltsquare.com
 */

namespace RLTSquare\Quickview\Observer;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class AddUpdateHandlesObserver add handles types
 */
class AddUpdateHandlesObserver implements ObserverInterface
{
    /** @var ScopeConfigInterface */
    protected ScopeConfigInterface $scopeConfig;

    /** @var HttpRequest */
    protected HttpRequest $request;

    /** @var StoreManagerInterface */
    protected StoreManagerInterface $storeManager;

    /** @var ProductRepositoryInterface */
    protected ProductRepositoryInterface $productRepository;

    /**
     * AddUpdateHandlesObserver constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param HttpRequest $request
     * @param StoreManagerInterface $storeManager
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        ScopeConfigInterface       $scopeConfig,
        HttpRequest                $request,
        StoreManagerInterface      $storeManager,
        ProductRepositoryInterface $productRepository
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
    }

    /**
     * @param Observer $observer
     * @return $this
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $layout = $observer->getData('layout');
        $fullActionName = $observer->getData('full_action_name');

        if ($fullActionName != 'rltsquare_quickview_catalog_product_view') {
            return $this;
        }

        $productId = $this->request->getParam('id');
        $storeId = $this->storeManager->getStore()->getId();

        if ($productId) {
            try {
                $product = $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                return $this;
            }

            $layout->getUpdate()->addHandle('rltsquare_quickview_catalog_product_view_type_' . $product->getTypeId());
        }

        return $this;
    }
}
