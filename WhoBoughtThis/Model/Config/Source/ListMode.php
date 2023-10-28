<?php
/**
 * NOTICE OF LICENSE
 * You may not sell, distribute, sub-license, rent, lease or lend complete or portion of software to anyone.
 *
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future.
 *
 * @package   RLTSquare_WhoBoughtThis
 * @copyright Copyright (c) 2022 RLTSquare (https://www.rltsquare.com)
 * @contacts  support@rltsquare.com
 * @license  See the LICENSE.md file in module root directory
 */
declare(strict_types=1);

namespace RLTSquare\WhoBoughtThis\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class ListMode
 * @package RLTSquare\WhoBoughtThis\Model\Config\Source
 */
class ListMode implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => '1', 'label' => __('Level-1')],
            ['value' => '2', 'label' => __('Level-2')],
            ['value' => '3', 'label' => __('Level-3')],
            ['value' => '4', 'label' => __('Level-4')],
            ['value' => '5', 'label' => __('Level-5')]
        ];
    }
}
