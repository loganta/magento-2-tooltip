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

namespace SM\Coachmarks\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Findtype
 *
 * @package SM\Coachmarks\Model\Config\Source
 */
class Findtype implements OptionSourceInterface
{
    const ID_ELEMENT_HTML = 'ID';
    const CLASS_ELEMENT_HTML = 'CLASS';

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::ID_ELEMENT_HTML,
                'label' => __('ID ELEMENT HTML')
            ],
            [
                'value' => self::CLASS_ELEMENT_HTML,
                'label' => __('CLASS ELEMENT HTML')
            ]
        ];

        return $options;
    }
}
