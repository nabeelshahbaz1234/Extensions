<?php
declare(strict_types=1);

/**
 * author:awais.rehman@rltsquare.com
 */

namespace RLTSquare\Quickview\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http as HttpRequest;

/**
 * Class ScopeConfigPlugin handle configuration
 */
class ScopeConfigPlugin
{
    /** @var HttpRequest */
    protected HttpRequest $request;

    /**
     * ResultPage constructor.
     * @param HttpRequest $request
     */
    public function __construct(HttpRequest $request)
    {
        $this->request = $request;
    }

    /**
     * @param ScopeConfigInterface $subject
     * @param \Closure $proceed
     * @param $path
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function aroundGetValue(
        ScopeConfigInterface $subject,
        \Closure             $proceed,
        $path,
        string               $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ) {
        $result = $proceed($path, $scopeType, $scopeCode);

        if (($path == 'checkout/cart/redirect_to_cart')) {
            $refererUrl = $this->request->getServer('HTTP_REFERER');
            if (!is_null($refererUrl) && str_contains($refererUrl, 'rltsquare_quickview/catalog_product/view')) {
                return false;
            }
        }

        return $result;
    }
}
