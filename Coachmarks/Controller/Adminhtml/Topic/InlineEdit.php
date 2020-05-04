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
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use SM\Coachmarks\Model\Topic;
use SM\Coachmarks\Model\TopicFactory;
use RuntimeException;

/**
 * Class InlineEdit
 *
 * @package SM\Coachmarks\Controller\Adminhtml\Topic
 */
class InlineEdit extends Action
{
    /**
     * JSON Factory
     *
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var TopicFactory
     */
    protected $topicFactory;

    /**
     * InlineEdit constructor.
     *
     * @param JsonFactory $jsonFactory
     * @param TopicFactory $topicFactory
     * @param Context $context
     */
    public function __construct(
        JsonFactory $jsonFactory,
        TopicFactory $topicFactory,
        Context $context
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->topicFactory = $topicFactory;

        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
        $postItems = $this->getRequest()->getParam('items', []);
        if (!(!empty($postItems) && $this->getRequest()->getParam('isAjax'))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }
        foreach (array_keys($postItems) as $topicId) {
            /** @var Topic $topic */
            $topic = $this->topicFactory->create()->load($topicId);
            try {
                $topicData = $postItems[$topicId];
                $topic->addData($topicData);
                $topic->save();
            } catch (RuntimeException $e) {
                $messages[] = $this->getErrorWithTopicId($topic, $e->getMessage());
                $error = true;
            } catch (Exception $e) {
                $messages[] = $this->getErrorWithTopicId(
                    $topic,
                    __('Something went wrong while saving the Tooltip.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * @param Topic $topic
     * @param $errorText
     *
     * @return string
     */
    protected function getErrorWithTopicId(Topic $topic, $errorText)
    {
        return '[Topic ID: ' . $topic->getId() . '] ' . $errorText;
    }
}
