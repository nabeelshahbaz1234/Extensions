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

namespace RLTSquare\RecentlyViewed\Helper;

use Magento\Customer\Model\Session;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use RLTSquare\RecentlyViewed\Model\History;
use RLTSquare\RecentlyViewed\Model\ResourceModel\History as ResourceModelHistory;
use RLTSquare\RecentlyViewed\Model\ResourceModel\History\CollectionFactory;

class RecentlyViewed
{
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $recentlyViewed;

    /**
     * @var History
     */
    protected History $historyModel;

    /**
     * @var Session
     */
    protected Session $customerSession;

    /**
     * @var DateTime
     */
    protected DateTime $date;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var ResourceModelHistory
     */
    protected ResourceModelHistory $recentlyViewedFactory;

    /**
     * RecentlyViewed constructor.
     * @param CollectionFactory $recentlyViewed
     * @param ResourceModelHistory $recentlyViewedFactory
     * @param History $historyModel
     * @param SessionFactory $customerSession
     * @param DateTime $date
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CollectionFactory     $recentlyViewed,
        ResourceModelHistory  $recentlyViewedFactory,
        History               $historyModel,
        SessionFactory        $customerSession,
        DateTime              $date,
        StoreManagerInterface $storeManager
    ) {
        $this->recentlyViewed = $recentlyViewed;
        $this->customerSession = $customerSession->create();
        $this->date = $date;
        $this->storeManager = $storeManager;
        $this->recentlyViewedFactory = $recentlyViewedFactory;
        $this->historyModel = $historyModel;
    }

    /**
     * @param $productId
     * @throws NoSuchEntityException
     */
    public function setRecentlyViewData($productId): void
    {
        if ($this->customerSession->isLoggedIn()) {
            $customerId = $this->customerSession->getCustomerId();
            $date = $this->date->gmtDate();
            $store_id = $this->storeManager->getStore()->getId();
            $this->saveData($customerId, $productId, $store_id, $date);
        }
    }

    /**
     * @param $customerId
     * @param $productId
     * @param $store_id
     * @param $date
     */
    public function saveData($customerId, $productId, $store_id, $date)
    {
        $historyCollection = null;
        // customer is logged in
        $historyCollection = $this->recentlyViewed->create();
        $historyCollection->addFieldToFilter('customer_id', $customerId);
        $historyCollection->addFieldToFilter('product_id', $productId);
        $historyCollection->getFirstItem();

        //Do not enter if same data already exists
        if (!$historyCollection->getData()) {
            /*SET DATA*/
            $this->historyModel->setCustomerId($customerId);
            $this->historyModel->setProductId($productId);
            $this->historyModel->setStoreId($store_id);
            $this->historyModel->setAddedAt($date);

            /*SAVE*/
            try {
                $this->recentlyViewedFactory->save($this->historyModel);
            } catch (\Exception $ex) {
                return false;
            }
        }
    }
}
