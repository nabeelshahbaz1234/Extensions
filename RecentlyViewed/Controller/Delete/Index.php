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

namespace RLTSquare\RecentlyViewed\Controller\Delete;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use RLTSquare\RecentlyViewed\Model\ResourceModel\History\CollectionFactory;

/**
 * Class Index
 * @package RLTSquare\RecentlyViewed\Controller\Delete
 */
class Index implements ActionInterface
{
    /**
     * @var PageFactory
     */
    protected PageFactory $resultPageFactory;

    /**
     * @var Session
     */
    protected Session $customerSession;

    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $recentlyViewedFactory;

    /**
     * @var ResponseInterface
     */
    protected ResponseInterface $response;

    /**
     * @var RedirectInterface
     */
    protected RedirectInterface $redirect;
    /**
     * @var ManagerInterface
     */
    private ManagerInterface $messageManager;
    /**
     * @var Redirect
     */
    private Redirect $resultRedirectFactory;
    /**
     * @var RequestInterface
     */
    private RequestInterface $requestInterface;
    /**
     * @var ResultFactory
     */
    private ResultFactory $resultFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param CollectionFactory $recentlyViewedFactory
     * @param RequestInterface $requestInterface
     * @param ManagerInterface $messageManager
     * @param Redirect $resultRedirectFactory
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        PageFactory       $resultPageFactory,
        Session           $customerSession,
        CollectionFactory $recentlyViewedFactory,
        RequestInterface $requestInterface,
        ManagerInterface $messageManager,
        Redirect $resultRedirectFactory,
        ResultFactory $resultFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->customerSession = $customerSession;
        $this->recentlyViewedFactory = $recentlyViewedFactory;
        $this->messageManager = $messageManager;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->requestInterface = $requestInterface;
        $this->resultFactory = $resultFactory;
    }

    /**
     * @return false|ResponseInterface|Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            if ($this->customerSession->isLoggedIn()) {
                // customer is logged in then delete the history of that customer
                $customerId = $this->customerSession->getCustomer()->getId();
                $historyCollection = $this->recentlyViewedFactory->create();
                $historyCollection->addFieldToFilter('customer_id', $customerId);
                $historyCollection->walk('delete');
            }
            /** @var  $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            /** @var Redirect $resultRedirect */
            return $resultRedirect->setPath('/');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage('History could not be deleted.');
            return false;
        }
    }

}
