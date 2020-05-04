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

namespace SM\Coachmarks\Block\Adminhtml\Tooltip\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;
use Magento\Config\Model\Config\Source\Enabledisable;
use Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory;
use Magento\Framework\Convert\DataObject;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use SM\Coachmarks\Helper\Data;
use SM\Coachmarks\Model\Config\Source\Type;
use Magento\Backend\Block\Widget\Button;
use SM\Coachmarks\Block\Adminhtml\Tooltip\Edit\Tab\Render\Topic;
use SM\Coachmarks\Model\Config\Source\Topics as TopicOption;
use Magento\Backend\Block\Widget\Form\Element\Dependence;
use SM\Coachmarks\Model\Config\Source\Findtype;
use SM\Coachmarks\Model\Config\Source\Position;

/**
 * Class Tooltip
 *
 * @package SM\Coachmarks\Block\Adminhtml\Tooltip\Edit\Tab
 */
class Tooltip extends Generic implements TabInterface
{
    /**
     * Type options
     *
     * @var Type
     */
    protected $typeOptions;

    /**
     * Status options
     *
     * @var Enabledisable
     */
    protected $statusOptions;

    /**
     * @var FieldFactory
     */
    protected $_fieldFactory;

    /**
     * @var DataObject
     */
    protected $_objectConverter;

    /**
     * @var WysiwygConfig
     */
    protected $_wysiwygConfig;

    /**
     * @var TopicOption
     */
    protected $topicOption;

    /**
     * @var Findtype
     */
    protected $findtypeOptions;

    /**
     * @var Findtype
     */
    protected $position;

    /**
     * Tooltip constructor.
     *
     * @param Type $typeOptions
     * @param Enabledisable $statusOptions
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param FieldFactory $fieldFactory
     * @param DataObject $objectConverter
     * @param WysiwygConfig $wysiwygConfig
     * @param TopicOption $topicOption
     * @param Findtype $findtype
     * @param array $data
     */
    public function __construct(
        Type $typeOptions,
        Enabledisable $statusOptions,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        FieldFactory $fieldFactory,
        DataObject $objectConverter,
        WysiwygConfig $wysiwygConfig,
        TopicOption $topicOption,
        Findtype $findtype,
        Position $position,
        array $data = []
    ) {
        $this->typeOptions = $typeOptions;
        $this->statusOptions = $statusOptions;
        $this->_fieldFactory = $fieldFactory;
        $this->_objectConverter = $objectConverter;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->topicOption = $topicOption;
        $this->findtypeOptions = $findtype;
        $this->position = $position;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return Generic
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \SM\Coachmarks\Model\Tooltip $tooltip */
        $tooltip = $this->_coreRegistry->registry('coachmarks_tooltip');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('tooltip_');
        $form->setFieldNameSuffix('tooltip');
        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('Tooltip Information'),
            'class' => 'fieldset-wide'
        ]);

        if ($tooltip->getId()) {
            $fieldset->addField(
                'tooltip_id',
                'hidden',
                ['name' => 'tooltip_id']
            );
        }

        $fieldset->addField('name', 'text', [
            'name' => 'name',
            'label' => __('Name'),
            'title' => __('Name'),
            'required' => true,
        ]);

        $fieldset->addField('status', 'select', [
            'name' => 'status',
            'label' => __('Status'),
            'title' => __('Status'),
            'values' => $this->statusOptions->toOptionArray(),
        ]);

        $fieldset->addField('topic_id', 'select', [
            'name' => 'topic_id',
            'label' => __('Asigned For Topic'),
            'title' => __('Asigned For Topic'),
            'required' => true,
            'note' => __('You should create Topic before create Tooltip.'),
            'values' => $this->topicOption->toOptionArray(),
        ]);

        $fieldset->addField('find_type', 'select', [
            'name' => 'find_type',
            'label' => __('Find Element Type'),
            'title' => __('Find Element Type'),
            'values' => $this->findtypeOptions->toOptionArray()
        ]);

        $fieldset->addField('id_element_html', 'text', [
            'name' => 'id_element_html',
            'label' => __('Id Element HTML'),
            'title' => __('Id Element HTML'),
            'required' => true,
            'note' => __('Important Note: Id element use for tooltip should be specified only for this element on source page!')
        ]);

        $fieldset->addField('class_element_html', 'text', [
            'name' => 'class_element_html',
            'label' => __('Class Element HTML'),
            'title' => __('Class Element HTML'),
            'required' => true,
            'note' => __('Important Note: Class use for tooltip should be specified only for this element on source page!')
        ]);

        $fieldset->addField('sort_order', 'text', [
            'name' => 'sort_order',
            'label' => __('Sort Order'),
            'title' => __('Sort Order'),
            'required' => true,
            'class' => 'validate-digits-range digits-range-0-999',
            'value' => '100',
            'note' => __('Value should be a number in range 0 - 999 and 0 is highest.')
        ]);

        $fieldset->addField('content', 'textarea', [
            'name' => 'content',
            'required' => true,
            'label' => __('Content'),
            'title' => __('Content')
        ]);

        $tooltipData = $this->_session->getData('coachmarks_tooltip_data', true);
        if ($tooltipData) {
            $tooltip->addData($tooltipData);
        } else {
            if (!$tooltip->getId()) {
                $tooltip->addData($tooltip->getDefaultValues());
            }
        }

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
                ->addFieldMap("tooltip_find_type", 'find_type')
                ->addFieldMap("tooltip_id_element_html", 'id_element_html')
                ->addFieldDependence('id_element_html', 'find_type', 'ID')
                ->addFieldMap("tooltip_class_element_html", 'class_element_html')
                ->addFieldDependence('class_element_html', 'find_type', 'CLASS')
        );

        $form->addValues($tooltip->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
