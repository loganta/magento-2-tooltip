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

use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use SM\Coachmarks\Controller\Adminhtml\Tooltip;

/**
 * Class Delete
 *
 * @package SM\Coachmarks\Controller\Adminhtml\Tooltip
 */
class Delete extends Tooltip
{
    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $this->tooltipFactory->create()
                ->load($this->getRequest()->getParam('tooltip_id'))
                ->delete();
            $this->messageManager->addSuccessMessage(__('The Tooltip has been deleted.'));
        } catch (Exception $e) {
            // display error message
            $this->messageManager->addErrorMessage($e->getMessage());
            // go back to edit form
            $resultRedirect->setPath(
                'coachmarks/*/edit',
                ['tooltip_id' => $this->getRequest()->getParam('tooltip_id')]
            );

            return $resultRedirect;
        }

        $resultRedirect->setPath('coachmarks/*/');

        return $resultRedirect;
    }
}
