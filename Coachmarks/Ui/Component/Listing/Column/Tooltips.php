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
class Tooltips extends Column
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
                    $id = $item['topic_id'];
                    $data = $this->helperData->getCountTooltipAsigned($id)->getSize();
                    $item[$this->getData('name')] = $this->_prepareItem($data);
                }
            }
        }

        return $dataSource;
    }

    /**
     * @param $data
     *
     * @return string
     */
    protected function _prepareItem($data)
    {
        $content = '<span class="tooltip-grid-topic"><b>' . __("No tooltip asigned") . '</b></span>';
        if ($data <= 1) {
            $content = '<span class="tooltip-grid-topic"><b>' . __($data . ' tooltip') . '</b></span>';
        } else {
            $content = '<span class="tooltip-grid-topic"><b>' . __($data . ' tooltips') . '</b></span>';
        }

        return $content;
    }
}
