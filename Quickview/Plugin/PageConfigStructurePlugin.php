<?php
declare(strict_types=1);

/**
 * author:awais.rehman@rltsquare.com
 */

namespace RLTSquare\Quickview\Plugin;

use Magento\Framework\View\Page\Config\Structure;
use RLTSquare\Quickview\Model\Config as QuickViewConfig;

/**
 * Class PageConfigStructurePlugin remove asset depends on configuration
 */
class PageConfigStructurePlugin
{

    /** @var QuickViewConfig */
    protected QuickViewConfig $config;

    /**
     * PageConfigStructurePlugin constructor.
     * @param QuickViewConfig $config
     */
    public function __construct(
        QuickViewConfig $config
    ) {
        $this->config = $config;
    }

    /**
     * @param Structure $subject
     * @param string $name
     * @param array $attributes
     * @return array
     */
    public function beforeAddAssets(
        Structure $subject,
        string    $name,
        array $attributes
    ) {

        if (!$this->config->isEnabled()) {
            if ($name == 'RLTSquare_Quickview::css/magnific-popup.css') {
                $subject->removeAssets($name);
            }
        }

        return [$name, $attributes];
    }
}
