<?php
/**
 * @category    SM
 * @package     SM_Coachmarks
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Logan Ta <phuongtt2@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Coachmarks\Block\Adminhtml\Tooltip\Edit\Tab\Render;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

/**
 * Class Status
 *
 * @package SM\Coachmarks\Block\Adminhtml\Tooltip\Edit\Tab\Render
 */
class Status extends AbstractRenderer
{
    /**
     * @param DataObject $row
     *
     * @return string
     */
    public function render(DataObject $row)
    {
        $status = $row->getData($this->getColumn()->getIndex());

        return $status === '1' ? __('Enable') : '<span class="tooltip-disabled"><b>' . __('Disabled') . '</b></span>';
    }
}
