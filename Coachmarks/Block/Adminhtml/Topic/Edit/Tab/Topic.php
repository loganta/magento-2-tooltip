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

namespace SM\Coachmarks\Block\Adminhtml\Topic\Edit\Tab;

use Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Source\Enabledisable;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Convert\DataObject;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime;
use Magento\Store\Model\System\Store;
use SM\Coachmarks\Model\Config\Source\Type;

/**
 * Class Topic
 *
 * @package SM\Coachmarks\Block\Adminhtml\Topic\Edit\Tab
 */
class Topic extends Generic implements TabInterface
{
    /**
     * Status options
     *
     * @var Enabledisable
     */
    protected $statusOptions;

    /**
     * @var Store
     */
    protected $_systemStore;

    /**
     * @var GroupRepositoryInterface
     */
    protected $_groupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;

    /**
     * @var DataObject
     */
    protected $_objectConverter;

    /**
     * @var Type
     */
    protected $actionType;

    /**
     * Topic constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Enabledisable $statusOptions
     * @param GroupRepositoryInterface $groupRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DataObject $objectConverter
     * @param Store $systemStore
     * @param Type $actionType
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Enabledisable $statusOptions,
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DataObject $objectConverter,
        Store $systemStore,
        Type $actionType,
        array $data = []
    ) {
        $this->statusOptions = $statusOptions;
        $this->_groupRepository = $groupRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_objectConverter = $objectConverter;
        $this->_systemStore = $systemStore;
        $this->actionType = $actionType;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Generic
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \SM\Coachmarks\Model\Topic $topic */
        $topic = $this->_coreRegistry->registry('coachmarks_topic');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('topic_');
        $form->setFieldNameSuffix('topic');
        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('Topic Information'),
            'class' => 'fieldset-wide'
        ]);
        if ($topic->getId()) {
            $fieldset->addField('topic_id', 'hidden', ['name' => 'topic_id']);
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
            'values' => array_merge(['' => ''], $this->statusOptions->toOptionArray()),
        ]);

        if (!$this->_storeManager->isSingleStoreMode()) {
            /** @var RendererInterface $rendererBlock */
            $rendererBlock = $this->getLayout()->createBlock(Element::class);
            $fieldset->addField('store_ids', 'multiselect', [
                'name' => 'store_ids',
                'label' => __('Store Views'),
                'title' => __('Store Views'),
                'required' => true,
                'values' => $this->_systemStore->getStoreValuesForForm(false, true)
            ])->setRenderer($rendererBlock);
            if (!$topic->hasData('store_ids')) {
                $topic->setStoreIds(0);
            }
        } else {
            $fieldset->addField('store_ids', 'hidden', [
                'name' => 'store_ids',
                'value' => $this->_storeManager->getStore()->getId()
            ]);
        }

        $fieldset->addField('action_type', 'select', [
            'name' => 'action_type',
            'label' => __('Action Type'),
            'title' => __('Action Type'),
            'values' => $this->actionType->toOptionArray()
        ]);

        $fieldset->addField('page_cms', 'text', [
            'name' => 'page_cms',
            'label' => __('Handle Name CMS Pages'),
            'title' => __('Handle Name CMS Pages'),
            'required' => false,
            'note' => __('Enter handle name of page to allow tooltip only work for that page. Work for pages have not path')
        ]);

        $fieldset->addField('page_url', 'text', [
            'name' => 'page_url',
            'label' => __('Include Pages Url Contain'),
            'title' => __('Include Pages Url Contain'),
            'required' => false,
            'note' => __('Page with URL containing the above path will be selected to tooltip only work for that page. Work for pages have path')
        ]);

        $fieldset->addField('description', 'textarea', [
            'name' => 'description',
            'label' => __('Description'),
            'title' => __('Description'),
            'required' => true,
        ]);

//        $fieldset->addField('sort_order', 'text', [
//            'name' => 'sort_order',
//            'label' => __('Sort Order'),
//            'title' => __('Sort Order'),
//            'required' => true,
//            'class' => 'validate-digits-range digits-range-0-999',
//            'value' => '100',
//            'note' => __('Value should be a number in range 0 - 999 and 0 is highest.')
//        ]);

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
                ->addFieldMap("topic_action_type", 'action_type')
                ->addFieldMap("topic_page_cms", 'page_cms')
                ->addFieldDependence('page_cms', 'action_type', 'page_cms')
                ->addFieldMap("topic_page_url", 'page_url')
                ->addFieldDependence('page_url', 'action_type', 'page_url')
        );

        $form->addValues($topic->getData());
        $this->setForm($form);

        return parent::_prepareForm();
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
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
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
}
