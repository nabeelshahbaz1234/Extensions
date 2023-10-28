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
 * @package   RLTSquare_DiscountFilter
 * @copyright Copyright (c) 2022 RLTSquare (https://www.rltsquare.com)
 * @contacts  support@rltsquare.com
 * @license  See the LICENSE.md file in module root directory
 */

namespace RLTSquare\DiscountFilter\Model\Layer\Filter;

use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\Layer\Filter\DataProvider\PriceFactory;
use Magento\Catalog\Model\Layer\Filter\Item\DataBuilder;
use Magento\Catalog\Model\Layer\Filter\ItemFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Rating
 * @package RLTSquare\DiscountFilter\Model\Layer\Filter
 */
class Rating extends AbstractFilter
{
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $productCollectionFactory;
    /**
     * @var PriceFactory
     */
    private $dataProvider;

    /**
     * Rating constructor.
     * @param ItemFactory $filterItemFactory
     * @param StoreManagerInterface $storeManager
     * @param Layer $layer
     * @param DataBuilder $itemDataBuilder
     * @param CollectionFactory $productCollectionFactory
     * @param PriceFactory $dataProviderFactory
     * @param array $data
     * @throws LocalizedException
     */
    public function __construct(
        ItemFactory           $filterItemFactory,
        StoreManagerInterface $storeManager,
        Layer                 $layer,
        DataBuilder           $itemDataBuilder,
        CollectionFactory     $productCollectionFactory,
        PriceFactory          $dataProviderFactory,
        array                 $data = []
    ) {
        parent::__construct($filterItemFactory, $storeManager, $layer, $itemDataBuilder, $data);
        $this->productCollectionFactory = $productCollectionFactory;
        $this->_requestVar = 'dis';
        $this->dataProvider = $dataProviderFactory->create(['layer' => $this->getLayer()]);
    }

    /**
     * @return mixed
     */
    public function getResetValue(): mixed
    {
        return $this->dataProvider->getResetValue();
    }

    /**
     * @param RequestInterface $request
     * @return $this|AbstractFilter
     */
    public function apply(RequestInterface $request)
    {
        $filter = $request->getParam($this->getRequestVar());
        if (!$filter || is_array($filter)) {
            return $this;
        }
        $filter = explode('-', $filter);
        list($from, $to) = $filter;
        $entity_id = [];
        $collection = $this->productCollectionFactory->create()
            ->addAttributeToSelect(['sku', 'price', 'special_price', 'special_to_date', 'special_from_date'])
            ->addAttributeToFilter('special_price', ['notnull' => true]);

        foreach ($collection as $product) {
            $price = $product->getPrice();
            if ($product->getTypeId() == "bundle") {
                $price = $product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue();
            }
            $sprice = $product->getSpecialPrice();
            $sprice_from_date = $product->getSpecialFromDate();
            $sprice_to_date = $product->getSpecialToDate();
            if ($price > 0 && isset($sprice_from_date) && isset($sprice_to_date) && !empty($sprice_from_date) && !empty($sprice_to_date)) {
                if (time() >= strtotime($sprice_from_date) && time() <= strtotime($sprice_to_date)) {
                    $dis = ($price - $sprice) * 100 / $price;
                    if ($dis >= $from && $dis <= $to) {
                        $entity_id[] = $product->getId();
                    }
                }
            }
        }
        $this->getLayer()
            ->getProductCollection()
            ->addAttributeToFilter('entity_id', ['in' => ($entity_id)]);
        return $this;
    }

    /**
     * @return Phrase
     */
    public function getName(): Phrase
    {
        return __('Discount');
    }

    /**
     * @return array
     */
    protected function _getItemsData(): array
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $scopeConfig = $objectManager->create('\Magento\Framework\App\Config\ScopeConfigInterface');
        if ($scopeConfig->getValue('discountFiltered/discountFilterGroup/isEnableDisable')) {
            $facets = [
                '0-20' => '<div class="rating-result"><span>' . __('1% to 20%') . '</span></div>',
                '21-40' => '<div class="rating-result"><span>' . __('21% to 40%') . '</span></div>',
                '41-60' => '<div class="rating-result"><span>' . __('41% to 60%') . '</span></div>',
                '61-80' => '<div class="rating-result"><span>' . __('61% to 80%') . '</span></div>',
                '81-100' => '<div class="rating-result"><span>' . __('81% to 100%') . '</span></div>',
            ];
            if (count($facets) > 1) {
                foreach ($facets as $key => $label) {
                    $filter = explode('-', $key);
                    list($from, $to) = $filter;
                    $collection = $this->getLayer()->getCurrentCategory()
                        ->getProductCollection()
                        ->addAttributeToSelect(['sku', 'price', 'special_price', 'special_to_date', 'special_from_date'])
                        ->addFieldToFilter('special_price', ['gt' => '5']);
                    $count1 = 0;
                    foreach ($collection as $product) {
                        $price = $product->getPrice();
                        if ($product->getTypeId() == "bundle") {
                            $price = $product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue();
                        }
                        $sprice = $product->getSpecialPrice();
                        $sprice_from_date = $product->getSpecialFromDate();
                        $sprice_to_date = $product->getSpecialToDate();
                        if ($price > 0 && isset($sprice_from_date) && isset($sprice_to_date) && !empty($sprice_from_date) && !empty($sprice_to_date)) {
                            if (time() >= strtotime($sprice_from_date) && time() <= strtotime($sprice_to_date)) {
                                $dis = ($price - $sprice) * 100 / $price;
                                if ($dis >= $from && $dis <= $to) {
                                    $count1++;
                                    [$product];
                                }
                            }
                        }
                    }

                    if ($count1 > 0) {
                        $this->itemDataBuilder->addItemData(
                            $label,
                            $key,
                            $count1
                        );
                    }
                }
            }
            return $this->itemDataBuilder->build();
        }
    }

    /**
     * @param $from
     * @param $to
     * @return array
     */
    public function getProduct($from, $to): array
    {
        $sku = [];
        $collection = $this->getLayer()
            ->getProductCollection()
            ->addAttributeToSelect(['sku', 'price', 'special_price'])
            ->addAttributeToFilter('special_price', ['notnull' => true]);
        foreach ($collection as $product) {
            $price = $product->getPrice();
            $sprice = $product->getSpecialPrice();
            $dis = ($price - $sprice) * 100 / $price;
            if ($dis >= $from && $dis <= $to) {
                $sku[] = $product->getId();
            }
        }
        return $sku;
    }
}
