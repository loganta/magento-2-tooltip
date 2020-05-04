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
use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Registry;
use SM\Coachmarks\Controller\Adminhtml\Tooltip;
use SM\Coachmarks\Model\TooltipFactory;
use RuntimeException;

/**
 * Class Save
 *
 * @package SM\Coachmarks\Controller\Adminhtml\Tooltip
 */
class Save extends Tooltip
{
    /**
     * JS helper
     *
     * @var Js
     */
    public $jsHelper;

    /**
     * Save constructor.
     *
     * @param TooltipFactory $topicFactory
     * @param Registry $registry
     * @param Js $jsHelper
     * @param Context $context
     */
    public function __construct(
        TooltipFactory $topicFactory,
        Registry $registry,
        Js $jsHelper,
        Context $context
    ) {
        $this->jsHelper    = $jsHelper;

        parent::__construct($topicFactory, $registry, $context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws FileSystemException
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($this->getRequest()->getPost('tooltip')) {
            $data   = $this->getRequest()->getPost('tooltip');
            $tooltip = $this->initTooltip();

            $data['topics_ids'] = (isset($data['topics_ids']) && $data['topics_ids'])
                ? explode(',', $data['topics_ids']) : [];
            if ($this->getRequest()->getPost('topics', false)) {
                $tooltip->setTagsData(
                    $this->jsHelper->decodeGridSerializedInput($this->getRequest()->getPost('topics', false))
                );
            }

            $tooltip->addData($data);

            $this->_eventManager->dispatch(
                'coachmarks_tooltip_prepare_save',
                [
                    'tooltip'  => $tooltip,
                    'request' => $this->getRequest()
                ]
            );
            try {
                $tooltip->save();
                $this->messageManager->addSuccessMessage(__('The tooltip has been saved.'));
                $this->_session->setSmCoachmarksTooltipData(false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'coachmarks/*/edit',
                        [
                            'tooltip_id' => $tooltip->getId(),
                            '_current'  => true
                        ]
                    );

                    return $resultRedirect;
                }
                $resultRedirect->setPath('coachmarks/*/');

                return $resultRedirect;
            } catch (RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Tooltip.'));
            }

            $this->_getSession()->setData('sm_coachmarks_tooltip_data', $data);
            $resultRedirect->setPath(
                'coachmarks/*/edit',
                [
                    'tooltip_id' => $tooltip->getId(),
                    '_current'  => true
                ]
            );

            return $resultRedirect;
        }

        $resultRedirect->setPath('coachmarks/*/');

        return $resultRedirect;
    }
}
