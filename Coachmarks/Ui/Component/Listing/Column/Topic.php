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

namespace SM\Coachmarks\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use SM\Coachmarks\Helper\Data as HelperData;

/**
 * Class Tooltips
 *
 * @package SM\Coachmarks\Ui\Component\Listing\Column
 */
class Topic extends Column
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * Tooltips constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param HelperData $helperData
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        HelperData $helperData,
        array $components = [],
        array $data = []
    ) {
        $this->helperData = $helperData;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['topic_id'])) {
                    $data = $this->helperData->getTopicCollection($item['topic_id'])->getFirstItem();
                    if ($data->getTopicId() == $item['topic_id']) {
                        $item[$this->getData('name')] = $this->_prepareItem($data);
                    } else {
                        $item[$this->getData('name')] = $this->_prepareItem($data);
                    }
                }
            }
        }

        return $dataSource;
    }

    /**
     * @param $topic
     *
     * @return string
     */
    protected function _prepareItem($topic)
    {
        $content = '<span class="topic-grid-tooltip"><b>' . __("Topic has not created") . '</b></span>';
        if ($topic->getName()) {
            $content = '<span class="topic-grid-tooltip"><b>' . __($topic->getName()) . '</b></span>';
        }

        return $content;
    }
}
