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
 * Class Visibility
 * @package RLTSquare\MostViewed\Model\Config\Source
 */
class Visibility implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'Product Page', 'label' => __('Product Page')],
            ['value' => 'Category Page', 'label' => __('Category Page')],
            ['value' => 'Home Page', 'label' => __('Home Page')]
        ];
    }
}
