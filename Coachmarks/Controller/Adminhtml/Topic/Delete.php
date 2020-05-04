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

namespace SM\Coachmarks\Controller\Adminhtml\Topic;

use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use SM\Coachmarks\Controller\Adminhtml\Topic;
use SM\Coachmarks\Model\Tooltip;

/**
 * Class Delete
 *
 * @package SM\Coachmarks\Controller\Adminhtml\Topic
 */
class Delete extends Topic
{
    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            /** @var Tooltip $tooltip */
            $this->topicFactory->create()
                ->load($this->getRequest()->getParam('topic_id'))
                ->delete();
            $this->messageManager->addSuccessMessage(__('The topic has been deleted.'));
        } catch (Exception $e) {
            // display error message
            $this->messageManager->addErrorMessage($e->getMessage());
            // go back to edit form
            $resultRedirect->setPath(
                'coachmarks/*/edit',
                ['topic_id' => $this->getRequest()->getParam('topic_id')]
            );

            return $resultRedirect;
        }

        $resultRedirect->setPath('coachmarks/*/');

        return $resultRedirect;
    }
}
