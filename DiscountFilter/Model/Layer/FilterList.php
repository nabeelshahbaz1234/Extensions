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

namespace RLTSquare\DiscountFilter\Model\Layer;

use Magento\Catalog\Model\Layer;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class FilterList
 * @package RLTSquare\DiscountFilter\Model\Layer
 */
class FilterList
{
    /**
     * @var ObjectManagerInterface
     */
    private ObjectManagerInterface $objectManager;

    /**
     * FilterList constructor.
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        Context                $context,
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * @param Layer\FilterList $subject
     * @param \Closure $proceed
     * @param Layer $layer
     * @return array|mixed
     */
    public function aroundGetFilters(
        Layer\FilterList $subject,
        \Closure         $proceed,
        Layer            $layer
    ) {
        $result = $proceed($layer);
        $result[] = $this->objectManager->create('RLTSquare\DiscountFilter\Model\Layer\Filter\Rating', ['layer' => $layer]);
        return $result;
    }
}
