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

namespace SM\Coachmarks\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * Class Tooltip
 *
 * @package SM\Coachmarks\Block\Adminhtml
 */
class Tooltip extends Container
{
    /**
     * constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_tooltip';
        $this->_blockGroup = 'SM_Coachmarks';
        $this->_headerText = __('Tooltips');
        $this->_addButtonLabel = __('Create New Tooltip');

        parent::_construct();
    }
}
