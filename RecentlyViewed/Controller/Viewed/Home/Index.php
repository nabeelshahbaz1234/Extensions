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

namespace RLTSquare\RecentlyViewed\Controller\Viewed\Home;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 * @package RLTSquare\RecentlyViewed\Controller\Viewed\Home
 */
class Index implements ActionInterface
{
    /**
     * @var PageFactory
     */
    protected PageFactory $resultPageFactory;

    /**
     * @var JsonFactory
     */
    protected JsonFactory $resultJsonFactory;
    /**
     * @var RequestInterface
     */
    private RequestInterface $requestInterface;
    /**
     * @var ManagerInterface
     */
    private ManagerInterface $messageManager;
    /**
     * @var Redirect
     */
    private Redirect $resultRedirectFactory;
    /**
     * @var ResultFactory
     */
    private ResultFactory $resultFactory;
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private \Magento\Framework\View\LayoutInterface $layout;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        PageFactory                             $resultPageFactory,
        JsonFactory                             $resultJsonFactory,
        RequestInterface                        $requestInterface,
        ManagerInterface                        $messageManager,
        Redirect                                $resultRedirectFactory,
        ResultFactory                           $resultFactory,
        \Magento\Framework\View\LayoutInterface $layout
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->requestInterface = $requestInterface;
        $this->messageManager = $messageManager;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->resultFactory = $resultFactory;
        $this->layout = $layout;
    }

    /**
     * @return Json
     */
    public function execute()
    {
        $fullActionName[] = $this->requestInterface->getParam('fullActionName');
        $block = $this->layout
            ->createBlock(
                'RLTSquare\RecentlyViewed\Block\Viewed',
                'recently.viewed',
                [
                    'data' => $fullActionName
                ]
            )
            ->setTemplate('RLTSquare_RecentlyViewed::slider/history/viewed.phtml');
        $html = $block->toHtml();
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData([
            'data' => $html
        ]);
    }
}
