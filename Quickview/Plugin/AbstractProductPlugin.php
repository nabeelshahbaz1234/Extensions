<?php
declare(strict_types=1);
/**
 * author:awais.rehman@rltsquare.com
 */

namespace RLTSquare\Quickview\Plugin;

use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Catalog\Block\Product\AbstractProduct;

/**
 * Class AbstractProductPlugin determine the handle
 */
class AbstractProductPlugin
{
    /** @var HttpRequest */
    protected HttpRequest $request;

    /**
     * AbstractProduct constructor.
     * @param HttpRequest $request
     */
    public function __construct(HttpRequest $request)
    {
        $this->request = $request;
    }

    /**
     * @param $result
     * @return bool
     */
    public function afterIsRedirectToCartEnabled(
        $result
    ) {
        $requestUri = $this->request->getRequestUri();

        if (str_contains($requestUri, 'rltsquare_quickview/catalog_product/view')) {
            return false;
        }

        return $result;
    }
}
