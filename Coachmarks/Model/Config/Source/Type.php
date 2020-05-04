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
 * Class Type
 *
 * @package SM\Coachmarks\Model\Config\Source
 */
class Type implements OptionSourceInterface
{
    const PAGE_URL_CONTAIN = 'page_url';
    const CMS_HANDLE_NAME = 'page_cms';

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::PAGE_URL_CONTAIN,
                'label' => __('Page URL Contain')
            ],
            [
                'value' => self::CMS_HANDLE_NAME,
                'label' => __('CMS Handle Name')
            ]
        ];

        return $options;
    }
}
