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

namespace SM\Coachmarks\Block\Adminhtml\Tooltip\Edit\Tab\Render;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

/**
 * Class Type
 *
 * @package SM\Coachmarks\Block\Adminhtml\Tooltip\Edit\Tab\Render
 */
class Type extends AbstractRenderer
{
    /**
     * Render tooltip type
     *
     * @param DataObject $row
     *
     * @return string
     */
    public function render(DataObject $row)
    {
        $type = $row->getData($this->getColumn()->getIndex());
        switch ($type) {
            case 0:
                $type = __('Image');
                break;
            case 1:
                $type = __('Advanced');
                break;
        }

        return $type;
    }
}
