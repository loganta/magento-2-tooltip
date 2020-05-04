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

namespace SM\Coachmarks\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\Registry;
use SM\Coachmarks\Model\TooltipFactory;

/**
 * Class Tooltip
 *
 * @package SM\Coachmarks\Controller\Adminhtml
 */
abstract class Tooltip extends Action
{
    /**
     * @var TooltipFactory
     */
    protected $tooltipFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * Result redirect factory
     *
     * @var RedirectFactory
     */

    /**
     * Tooltip constructor.
     *
     * @param TooltipFactory $tooltipFactory
     * @param Registry $coreRegistry
     * @param Context $context
     */
    public function __construct(
        TooltipFactory $tooltipFactory,
        Registry $coreRegistry,
        Context $context
    ) {
        $this->tooltipFactory = $tooltipFactory;
        $this->coreRegistry = $coreRegistry;

        parent::__construct($context);
    }

    /**
     * @return \SM\Coachmarks\Model\Tooltip
     */
    protected function initTooltip()
    {
        $tooltipId = (int)$this->getRequest()->getParam('tooltip_id');
        /** @var \SM\Coachmarks\Model\Tooltip $tooltip */
        $tooltip = $this->tooltipFactory->create();
        if ($tooltipId) {
            $tooltip->load($tooltipId);
        }
        $this->coreRegistry->register('coachmarks_tooltip', $tooltip);

        return $tooltip;
    }
}
