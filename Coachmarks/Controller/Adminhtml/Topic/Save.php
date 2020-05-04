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

namespace SM\Coachmarks\Controller\Adminhtml\Topic;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use RuntimeException;
use SM\Coachmarks\Controller\Adminhtml\Topic;
use SM\Coachmarks\Model\TopicFactory;
use Zend_Filter_Input;

/**
 * Class Save
 *
 * @package SM\Coachmarks\Controller\Adminhtml\Topic
 */
class Save extends Topic
{
    /**
     * @var Js
     */
    protected $jsHelper;

    /**
     * @var Date
     */
    protected $_dateFilter;

    /**
     * Save constructor.
     *
     * @param Js $jsHelper
     * @param TopicFactory $topicFactory
     * @param Registry $registry
     * @param Context $context
     * @param Date $dateFilter
     */
    public function __construct(
        Js $jsHelper,
        TopicFactory $topicFactory,
        Registry $registry,
        Context $context,
        Date $dateFilter
    ) {
        $this->jsHelper = $jsHelper;
        $this->_dateFilter = $dateFilter;

        parent::__construct($topicFactory, $registry, $context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($this->getRequest()->getPost('topic')) {
            $data = $this->_filterData($this->getRequest()->getPost('topic'));
            $topic = $this->initTopic();

            $tooltips = $this->getRequest()->getPost('tooltips', -1);
            if ($tooltips != -1) {
                $topic->setTooltipsData($this->jsHelper->decodeGridSerializedInput($tooltips));
            }
            $topic->addData($data);

            $this->_eventManager->dispatch(
                'coachmarks_tooltip_prepare_save',
                [
                    'topic' => $topic,
                    'request' => $this->getRequest()
                ]
            );

            try {
                $topic->save();
                $this->messageManager->addSuccessMessage(__('The topic has been saved.'));
                $this->_session->setSmCoachmarksTopicData(false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'coachmarks/*/edit',
                        [
                            'topic_id' => $topic->getId(),
                            '_current' => true
                        ]
                    );

                    return $resultRedirect;
                }
                $resultRedirect->setPath('coachmarks/*/');

                return $resultRedirect;
            } catch (RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the topic.'));
            }

            $this->_getSession()->setSmCoachmarksTopicData($data);
            $resultRedirect->setPath(
                'coachmarks/*/edit',
                [
                    'topic_id' => $topic->getId(),
                    '_current' => true
                ]
            );

            return $resultRedirect;
        }

        $resultRedirect->setPath('coachmarks/*/');

        return $resultRedirect;
    }

    /**
     * @param $data
     *
     * @return mixed
     */
    protected function _filterData($data)
    {
        $inputFilter = new Zend_Filter_Input(['from_date' => $this->_dateFilter], [], $data);
        $data = $inputFilter->getUnescaped();

        if ($this->getRequest()->getParam('tooltips')) {
            $data['tooltip_ids'] = $this->getRequest()->getParam('tooltips');
        }

        return $data;
    }
}
