<?php
declare(strict_types=1);

/**
 * author : awais.rehman@rltsquare.com
 */

namespace RLTSquare\Quickview\Block;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use RLTSquare\Quickview\Model\Config as QuickViewConfig;

/**
 * Class Initialize block init the js
 */
class Initialize extends Template
{
    /** @var QuickViewConfig */
    protected QuickViewConfig $config;

    /**
     * @param QuickViewConfig $config
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        QuickViewConfig $config,
        Context         $context,
        array           $data = []
    ) {
        $this->config = $config;
        parent::__construct($context, $data);
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getConfig()
    {
        return [
            'baseUrl' => $this->_storeManager->getStore()->getBaseUrl()
        ];
    }
}
