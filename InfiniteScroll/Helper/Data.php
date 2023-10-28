<?php
declare(strict_types=1);

namespace RLTSquare\InfiniteScroll\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Helper Class
 */
class Data extends AbstractHelper
{
    /**
     * @var array
     */
    protected $configModule;

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
        $this->configModule = $this->getConfig(strtolower($this->_getModuleName()));
    }

    /**
     * @param string $cfg
     * @return ScopeConfigInterface|mixed
     */
    public function getConfig($cfg = '')
    {
        if ($cfg) {
            return $this->scopeConfig->getValue($cfg, ScopeInterface::SCOPE_STORE);
        }
        return $this->scopeConfig;
    }

    /**
     * @param string $cfg
     * @param null $value
     * @return array|ScopeConfigInterface|mixed|null
     */
    public function getConfigModule($cfg = '', $value = null)
    {
        $values = $this->configModule;
        if (!$cfg) {
            return $values;
        }
        $config = explode('/', $cfg);
        $end = count($config) - 1;
        foreach ($config as $key => $vl) {
            if (isset($values[$vl])) {
                if ($key == $end) {
                    $value = $values[$vl];
                } else {
                    $values = $values[$vl];
                }
            }
        }
        return $value;
    }
}
