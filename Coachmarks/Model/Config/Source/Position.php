<?php
/**
 * @category SM
 * @package SM_Coachmarks
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author Logan Ta <phuongtt2@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

declare(strict_types=1);

namespace SM\Coachmarks\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Position
 * @package SM\Coachmarks\Model\Config\Source
 */
class Position implements OptionSourceInterface
{
    const TOP = 'top';
    const RIGHT = 'right';
    const BOTTOM = 'bottom';
    const LEFT = 'left';

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::TOP,
                'label' => __('Top of target')
            ],
            [
                'value' => self::RIGHT,
                'label' => __('Right of target')
            ],
            [
                'value' => self::BOTTOM,
                'label' => __('Bottom of target')
            ],
            [
            'value' => self::LEFT,
            'label' => __('Left of target')
            ]
        ];

        return $options;
    }
}
