<?php
/**
 * NOTICE OF LICENSE
 * You may not sell, distribute, sub-license, rent, lease or lend complete or portion of software to anyone.
 *
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future.
 *
 * @package   RLTSquare_MostViewed
 * @copyright Copyright (c) 2022 RLTSquare (https://www.rltsquare.com)
 * @contacts  support@rltsquare.com
 * @license  See the LICENSE.md file in module root directory
 */

namespace RLTSquare\MostViewed\Model\Config\Source;
use Magento\Framework\Data\OptionSourceInterface;
/**
 * Class DisplayLimit
 * @package RLTSquare\MostViewed\Model\Config\Source
 */
class DisplayLimit implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => '10', 'label' => __('10')],
            ['value' => '20', 'label' => __('20')],
            ['value' => '30', 'label' => __('30')],
            ['value' => '40', 'label' => __('40')],
            ['value' => '50', 'label' => __('50')],
            ['value' => '60', 'label' => __('60')],
            ['value' => '70', 'label' => __('70')],
            ['value' => '80', 'label' => __('80')],
            ['value' => '90', 'label' => __('90')],
            ['value' => '100', 'label' => __('100')],
        ];
    }
}
