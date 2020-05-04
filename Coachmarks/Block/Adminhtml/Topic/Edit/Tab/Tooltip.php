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

namespace SM\Coachmarks\Block\Adminhtml\Topic\Edit\Tab;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Helper\Data as backendHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use SM\Coachmarks\Block\Adminhtml\Tooltip\Edit\Tab\Render\Status;
use SM\Coachmarks\Block\Adminhtml\Tooltip\Edit\Tab\Render\LinkTooltip;
use SM\Coachmarks\Model\TooltipFactory;
use SM\Coachmarks\Model\ResourceModel\Tooltip\Collection;
use SM\Coachmarks\Model\ResourceModel\Tooltip\CollectionFactory as TooltipCollectionFactory;
use Magento\Framework\Stdlib\DateTime;

/**
 * Class Tooltip
 *
 * @package SM\Coachmarks\Block\Adminhtml\Topic\Edit\Tab
 */
class Tooltip extends Extended implements TabInterface
{
    /**
     * @var TooltipCollectionFactory
     */
    protected $tooltipCollectionFactory;

    /**
     * Registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var TooltipFactory
     */
    protected $tooltipFactory;

    /**
     * Tooltip constructor.
     *
     * @param TooltipCollectionFactory $tooltipCollectionFactory
     * @param Registry $coreRegistry
     * @param TooltipFactory $tooltipFactory
     * @param Context $context
     * @param backendHelper $backendHelper
     * @param array $data
     */
    public function __construct(
        TooltipCollectionFactory $tooltipCollectionFactory,
        Registry $coreRegistry,
        TooltipFactory $tooltipFactory,
        Context $context,
        backendHelper $backendHelper,
        array $data = []
    ) {
        $this->tooltipCollectionFactory = $tooltipCollectionFactory;
        $this->coreRegistry = $coreRegistry;
        $this->tooltipFactory = $tooltipFactory;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Set grid params
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('tooltip_grid');
        $this->setDefaultSort('sort_order');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * @return Extended|void
     */
    protected function _prepareCollection()
    {
        /** @var Collection $collection */
        $collection = $this->tooltipCollectionFactory->create();

        $collection->addFieldToFilter('topic_id', $this->getTopic()->getId());

        $this->setCollection($collection);

        parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * @return $this|Extended
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('tooltip_id', [
            'header'           => __('ID'),
            'sortable'         => true,
            'index'            => 'tooltip_id',
            'type'             => 'number',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
        ]);

        $this->addColumn('name', [
            'header'           => __('Name'),
            'index'            => 'name',
            'header_css_class' => 'col-name',
            'column_css_class' => 'col-name'
        ]);

        $this->addColumn('find_type', [
            'header'           => __('Find Type'),
            'index'            => 'find_type',
            'header_css_class' => 'col-name',
            'column_css_class' => 'col-name'
        ]);

        $this->addColumn('id_element_html', [
            'header'           => __('Id Element HTML'),
            'index'            => 'id_element_html',
            'header_css_class' => 'col-name',
            'column_css_class' => 'col-name'
        ]);

        $this->addColumn('class_element_html', [
            'header'           => __('Class Element HTML'),
            'index'            => 'class_element_html',
            'header_css_class' => 'col-name',
            'column_css_class' => 'col-name'
        ]);

        $this->addColumn('sort_order', [
            'header'           => __('Sort Order'),
            'index'            => 'sort_order',
            'header_css_class' => 'col-name',
            'column_css_class' => 'col-name'
        ]);

        $this->addColumn('status', [
            'header'           => __('Status'),
            'index'            => 'status',
            'header_css_class' => 'col-status',
            'column_css_class' => 'col-status',
            'filter' => false,
            'renderer'         => Status::class
        ]);

        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $this->addColumn('created_at', [
            'header'           => __('Created At'),
            'index'            => 'created_at',
            'input_format' => DateTime::DATE_INTERNAL_FORMAT,
            'date_format'  => $dateFormat,
            'filter' => false
        ]);

        $this->addColumn('action_link', [
            'header'           => __('Edit Link'),
            'index'            => 'tooltip_id',
            'header_css_class' => 'col-action_link',
            'column_css_class' => 'col-action_link',
            'filter' => false,
            'renderer'         => LinkTooltip::class
        ]);

        return $this;
    }

    /**
     * Retrieve selected Tooltips
     * @return array
     */
    protected function _getSelectedTooltips()
    {
        $tooltips = $this->getTopicTooltips();

        return $tooltips;
    }

    /**
     * Retrieve selected Topics
     * @return array
     */
    public function getSelectedTopics()
    {
        $selected = $this->getTopic()->getTopicsPosition();
        if (!is_array($selected)) {
            $selected = [];
        } else {
            foreach ($selected as $key => $value) {
                $selected[$key] = ['position' => $value];
            }
        }

        return $selected;
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $item
     *
     * @return string
     */
    public function getRowUrl($item)
    {
        return '';
    }

    /**
     * get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            '*/*/tooltipsGrid',
            [
                'topic_id' => $this->getTopic()->getId()
            ]
        );
    }

    /**
     * @return mixed
     */
    public function getTopic()
    {
        return $this->coreRegistry->registry('coachmarks_topic');
    }

    /**
     * @param Column $column
     *
     * @return $this|Extended
     * @throws LocalizedException
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() === 'in_tooltips') {
            $tooltipIds = $this->_getSelectedTooltips();
            if (empty($tooltipIds)) {
                $tooltipIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('main_table.tooltip_id', ['in' => $tooltipIds]);
            } else {
                if ($tooltipIds) {
                    $this->getCollection()->addFieldToFilter('main_table.tooltip_id', ['nin' => $tooltipIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return __('Tooltips Asigned');
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('coachmarks/topic/tooltips', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }
}
