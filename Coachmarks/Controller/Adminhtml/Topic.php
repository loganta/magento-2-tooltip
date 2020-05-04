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

namespace SM\Coachmarks\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use SM\Coachmarks\Model\TopicFactory;

/**
 * Class Topic
 *
 * @package SM\Coachmarks\Controller\Adminhtml
 */
abstract class Topic extends Action
{
    /**
     * @var TopicFactory
     */
    protected $topicFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * Topic constructor.
     *
     * @param TopicFactory $topicFactory
     * @param Registry $coreRegistry
     * @param Context $context
     */
    public function __construct(
        TopicFactory $topicFactory,
        Registry $coreRegistry,
        Context $context
    ) {
        $this->topicFactory = $topicFactory;
        $this->coreRegistry = $coreRegistry;

        parent::__construct($context);
    }

    /**
     * @return \SM\Coachmarks\Model\Topic
     */
    protected function initTopic()
    {
        $topicId = (int)$this->getRequest()->getParam('topic_id');
        /** @var \SM\Coachmarks\Model\Topic $topic */
        $topic = $this->topicFactory->create();
        if ($topicId) {
            $topic->load($topicId);
        }
        $this->coreRegistry->register('coachmarks_topic', $topic);

        return $topic;
    }
}
