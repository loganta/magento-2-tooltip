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

namespace SM\Coachmarks\Block\Adminhtml\Tooltip;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use SM\Coachmarks\Model\Tooltip;

/**
 * Class Edit
 *
 * @package SM\Coachmarks\Block\Adminhtml\Tooltip
 */
class Edit extends Container
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * constructor
     *
     * @param Registry $coreRegistry
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Registry $coreRegistry,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Initialize Tooltip edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'tooltip_id';
        $this->_blockGroup = 'SM_Coachmarks';
        $this->_controller = 'adminhtml_tooltip';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Tooltip'));
        $this->buttonList->add(
            'save-and-continue',
            [
                'label'          => __('Save and Continue Edit'),
                'class'          => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event'  => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ],
            -100
        );
        $this->buttonList->update('delete', 'label', __('Delete Tooltip'));
    }

    /**
     * Retrieve text for header element depending on loaded Tooltip
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var Tooltip $tooltip */
        $tooltip = $this->getTooltip();
        if ($tooltip->getId()) {
            return __("Edit Tooltip '%1'", $this->escapeHtml($tooltip->getName()));
        }

        return __('New Tooltip');
    }

    /**
     * @return mixed
     */
    public function getTooltip()
    {
        return $this->coreRegistry->registry('coachmarks_tooltip');
    }
}
