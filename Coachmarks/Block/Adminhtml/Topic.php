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
 * Class Topic
 *
 * @package SM\Coachmarks\Block\Adminhtml
 */
class Topic extends Container
{
    /**
     * constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_topic';
        $this->_blockGroup = 'SM_Coachmarks';
        $this->_headerText = __('Topics');
        $this->_addButtonLabel = __('Create New Topic');

        parent::_construct();
    }
}
