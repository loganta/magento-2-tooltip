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

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use SM\Coachmarks\Controller\Adminhtml\Topic;
use SM\Coachmarks\Model\TopicFactory;

/**
 * Class Edit
 *
 * @package SM\Coachmarks\Controller\Adminhtml\Topic
 */
class Edit extends Topic
{
    const ADMIN_RESOURCE = 'SM_Coachmarks::topic';

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
     * @param TopicFactory $topicFactory
     * @param Registry $registry
     * @param Context $context
     */
    public function __construct(
        PageFactory $resultPageFactory,
        TopicFactory $topicFactory,
        Registry $registry,
        Context $context
    ) {
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($topicFactory, $registry, $context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|ResponseInterface|Redirect|ResultInterface|Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('topic_id');
        /** @var \SM\Coachmarks\Model\Topic $topic */
        $topic = $this->initTopic();

        if ($id) {
            $topic->load($id);
            if (!$topic->getId()) {
                $this->messageManager->addErrorMessage(__('This topic no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath(
                    'coachmarks/*/edit',
                    [
                        'topic_id' => $topic->getId(),
                        '_current'  => true
                    ]
                );

                return $resultRedirect;
            }
        }

        $data = $this->_session->getData('coachmarks_topic_data', true);
        if (!empty($data)) {
            $topic->setData($data);
        }

        /** @var \Magento\Backend\Model\View\Result\Page|Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('SM_Coachmarks::topic');
        $resultPage->getConfig()->getTitle()
            ->set(__('Topics'))
            ->prepend($topic->getId() ? $topic->getName() : __('New Topic'));

        return $resultPage;
    }
}
