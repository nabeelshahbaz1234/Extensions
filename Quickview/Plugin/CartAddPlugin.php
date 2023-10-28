<?php
declare(strict_types=1);

/**
 * author:awais.rehman@rltsquare.com
 */

namespace RLTSquare\Quickview\Plugin;

use Magento\Checkout\Controller\Cart\Add;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\App\Request\Http as HttpRequest;

/**
 * Class CartAddPlugin handle the add to cart action
 */
class CartAddPlugin
{
    /** @var HttpRequest */
    protected HttpRequest $request;

    /**
     * @var SerializerInterface
     */
    protected SerializerInterface $serializer;

    /**
     * ResultPage constructor.
     * @param HttpRequest $request
     * @param SerializerInterface $serializer
     */
    public function __construct(
        HttpRequest $request,
        SerializerInterface $serializer
    ) {
        $this->request = $request;
        $this->serializer = $serializer;
    }

    /**
     * @param Add $subject
     * @param $result
     * @return mixed
     */
    public function afterExecute(
        Add $subject,
        $result
    ) {

        $refererUrl = $this->request->getServer('HTTP_REFERER');

        if (str_contains($refererUrl, 'rltsquare_quickview/catalog_product/view')) {
            return $subject->getResponse()->representJson($this->serializer->serialize([]));
        }
        return $result;
    }
}
