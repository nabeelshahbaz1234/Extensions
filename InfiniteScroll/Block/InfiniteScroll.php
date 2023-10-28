<?php
declare(strict_types=1);

namespace RLTSquare\InfiniteScroll\Block;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class InfiniteScroll
 */
class InfiniteScroll extends Template
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * InfiniteScroll constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Context               $context,
        StoreManagerInterface $storeManager,
        array                 $data = []
    ) {
        parent::__construct($context, $data);
        $this->storeManager = $storeManager;
    }

    /**
     * @param null $img
     * @return string
     * @throws NoSuchEntityException
     */
    public function getMedia($img = null)
    {
        $urlMedia = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        if ($img) {
            return $urlMedia . $img;
        }
        return $urlMedia;
    }
}
