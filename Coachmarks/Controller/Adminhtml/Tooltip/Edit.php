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

namespace SM\Coachmarks\Controller\Adminhtml\Tooltip;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use SM\Coachmarks\Controller\Adminhtml\Tooltip;
use SM\Coachmarks\Model\TooltipFactory;

/**
 * Class Edit
 *
 * @package SM\Coachmarks\Controller\Adminhtml\Tooltip
 */
class Edit extends Tooltip
{
    const ADMIN_RESOURCE = 'SM_Coachmarks::tooltip';

    /**
     * Page factory
     *
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Edit constructor.
     *
     * @param PageFactory $resultPageFactory
     * @param TooltipFactory $tooltipFactory
     * @param Registry $registry
     * @param Context $context
     */
    public function __construct(
        PageFactory $resultPageFactory,
        TooltipFactory $tooltipFactory,
        Registry $registry,
        Context $context
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($tooltipFactory, $registry, $context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|ResponseInterface|Redirect|ResultInterface|Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('tooltip_id');
        /** @var \SM\Coachmarks\Model\Tooltip $tooltip */
        $tooltip = $this->initTooltip();

        if ($id) {
            $tooltip->load($id);
            if (!$tooltip->getId()) {
                $this->messageManager->addErrorMessage(__('This Tooltip no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath(
                    'coachmarks/*/edit',
                    [
                        'tooltip_id' => $tooltip->getId(),
                        '_current'  => true
                    ]
                );

                return $resultRedirect;
            }
        }

        $data = $this->_session->getData('coachmarks_tooltip_data', true);
        if (!empty($data)) {
            $tooltip->setData($data);
        }

        /** @var \Magento\Backend\Model\View\Result\Page|Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('SM_Coachmarks::tooltip');
        $resultPage->getConfig()->getTitle()
            ->set(__('Tooltips'))
            ->prepend($tooltip->getId() ? $tooltip->getName() : __('New Tooltip'));

        return $resultPage;
    }
}
