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

namespace RLTSquare\RecentlyViewed\Observer;

use Magento\CatalogUrlRewrite\Model\ResourceModel\Category\Product;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use RLTSquare\RecentlyViewed\Helper\RecentlyViewed;

/**
 * Class View
 * @package RLTSquare\RecentlyViewed\Observer\Product
 */
class SaveUserForProduct implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var RecentlyViewed
     */
    protected RecentlyViewed $recentlyViewed;

    /**
     * @var Product
     */
    protected Product $productUrlRewriteResource;

    /**
     * @var UrlInterface
     */
    protected UrlInterface $urlInterface;

    /**
     * SaveUserForProduct constructor.
     * @param Product $productUrlRewriteResource
     * @param UrlInterface $urlInterface
     * @param RecentlyViewed $recentlyViewed
     */
    public function __construct(
        Product        $productUrlRewriteResource,
        UrlInterface   $urlInterface,
        RecentlyViewed $recentlyViewed
    ) {
        $this->recentlyViewed = $recentlyViewed;
        $this->productUrlRewriteResource = $productUrlRewriteResource;
        $this->urlInterface = $urlInterface;
    }

    /**
     * @param Observer $observer
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $request = $observer->getRequest();
        $productId = $this->getProductId($request);

        if (isset($productId)) {
            $this->recentlyViewed->setRecentlyViewData($productId);
        }
    }

    /**
     * @param $request
     */
    private function getProductId($request)
    {
        $productId = null;
        if ($request->getParam('id') && $request->getControllerName() === 'product') {
            $productId = $request->getParam('id');
        } else {
            $pathInfo = $request->getPathInfo();
            $preparedPathInfo = ltrim(trim($pathInfo), "/");

            $connection = $this->productUrlRewriteResource->getConnection();
            $table = $this->productUrlRewriteResource->getTable('url_rewrite');
            $select = $connection->select();
            $select->from($table, ['entity_id'])
                ->where('entity_type = :entity_type')
                ->where('request_path LIKE :request_path');

            $result = $connection->fetchCol(
                $select,
                ['entity_type' => 'product', 'request_path' => $preparedPathInfo]
            );
            $productId = $result[0] ?? null;
        }

        return $productId;
    }
}
