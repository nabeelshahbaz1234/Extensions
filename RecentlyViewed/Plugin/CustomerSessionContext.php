<?php
declare(strict_types=1);
/**
 * @author RLTSquare Team
 * Created by PhpStorm
 * User: Umer
 * Date: 24/11/21
 * Time: 5:51 PM
 */

namespace RLTSquare\RecentlyViewed\Plugin;

use Magento\Customer\Model\Session;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Http\Context;
use Magento\Framework\App\RequestInterface;

class CustomerSessionContext
{
    /**
     * @var Session
     */
    protected Session $customerSession;

    /**
     * @var Context
     */
    protected Context $httpContext;

    /**
     * @param Session $customerSession
     * @param Context $httpContext
     */
    public function __construct(
        Session $customerSession,
        Context $httpContext
    )
    {
        $this->customerSession = $customerSession;
        $this->httpContext = $httpContext;
    }

    /**
     * @param ActionInterface $subject
     * @param \Closure $proceed
     * @param RequestInterface $request
     * @return mixed
     */
    public function aroundDispatch(
        ActionInterface  $subject,
        \Closure         $proceed,
        RequestInterface $request
    )
    {
        $this->httpContext->setValue(
            'customer_id',
            $this->customerSession->getCustomerId(),
            false
        );
        return $proceed($request);
    }
}
