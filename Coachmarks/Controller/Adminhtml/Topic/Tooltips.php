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
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Layout;
use Magento\Framework\View\Result\LayoutFactory;
use SM\Coachmarks\Block\Adminhtml\Topic\Edit\Tab\Tooltip;
use SM\Coachmarks\Controller\Adminhtml\Topic;
use SM\Coachmarks\Model\TopicFactory;

/**
 * Class Tooltips
 *
 * @package SM\Coachmarks\Controller\Adminhtml\Topic
 */
class Tooltips extends Topic
{
    /**
     * Result layout factory
     *
     * @var LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * Tooltips constructor.
     *
     * @param LayoutFactory $resultLayoutFactory
     * @param TopicFactory $topicFactory
     * @param Registry $registry
     * @param Context $context
     */
    public function __construct(
        LayoutFactory $resultLayoutFactory,
        TopicFactory $topicFactory,
        Registry $registry,
        Context $context
    ) {
        $this->resultLayoutFactory = $resultLayoutFactory;

        parent::__construct($topicFactory, $registry, $context);
    }

    /**
     * @return Layout
     */
    public function execute()
    {
        $this->initTopic();
        $resultLayout = $this->resultLayoutFactory->create();
        /** @var Tooltip $tooltipsBlock */
        $tooltipsBlock = $resultLayout->getLayout()->getBlock('topic.edit.tab.tooltip');
        if ($tooltipsBlock) {
            $tooltipsBlock->setTopicTooltips($this->getRequest()->getPost('topic_tooltips', null));
        }

        return $resultLayout;
    }
}
