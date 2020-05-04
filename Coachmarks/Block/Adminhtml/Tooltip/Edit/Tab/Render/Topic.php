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

namespace SM\Coachmarks\Block\Adminhtml\Tooltip\Edit\Tab\Render;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\Multiselect;
use Magento\Framework\Escaper;
use SM\Coachmarks\Helper\Data;
use SM\Coachmarks\Model\ResourceModel\Topic\Collection;
use SM\Coachmarks\Model\ResourceModel\Topic\CollectionFactory as TopicCollectionFactory;

/**
 * Class Topic
 *
 * @package SM\Coachmarks\Block\Adminhtml\Tooltip\Edit\Tab\Render
 */
class Topic extends Multiselect
{
    /**
     * Authorization
     *
     * @var AuthorizationInterface
     */
    public $authorization;

    /**
     * @var TopicCollectionFactory
     */
    public $collectionFactory;

    /**
     * @var Data
     */
    public $helperData;

    /**
     * Topic constructor.
     *
     * @param Factory $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param TopicCollectionFactory $collectionFactory
     * @param AuthorizationInterface $authorization
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        TopicCollectionFactory $collectionFactory,
        AuthorizationInterface $authorization,
        Data $helperData,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->authorization     = $authorization;
        $this->helperData = $helperData;

        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    /**
     * @inheritdoc
     */
    public function getElementHtml()
    {
        $html = '<div class="admin__field-control admin__control-grouped">';
        $html .= '<div id="tooltip-topic-select" class="admin__field" data-bind="scope:\'coachmarks\'" data-index="index">';
        $html .= '<!-- ko foreach: elems() -->';
        $html .= '<input name="tooltip[topics_ids]" data-bind="value: value" style="display: none"/>';
        $html .= '<!-- ko template: elementTmpl --><!-- /ko -->';
        $html .= '<!-- /ko -->';
        $html .= '</div>';

        $html .= $this->getAfterElementHtml();

        return $html;
    }

    /**
     * Attach Blog Tag suggest widget initialization
     *
     * @return string
     */
    public function getAfterElementHtml()
    {
        $html = '<script type="text/x-magento-init">
            {
                "*": {
                    "Magento_Ui/js/core/app": {
                        "components": {
                            "coachmarks": {
                                "component": "uiComponent",
                                "children": {
                                    "tooltip_select_topic": {
                                        "component": "Magento_Catalog/js/components/new-category",
                                        "config": {
                                            "filterOptions": true,
                                            "disableLabel": true,
                                            "chipsEnabled": true,
                                            "levelsVisibility": "1",
                                            "elementTmpl": "ui/grid/filters/elements/ui-select",
                                            "options": ' . $this->helperData->jsonEncode($this->getTopicCollection()) . ',
                                            "value": ' . $this->helperData->jsonEncode($this->getValues()) . ',
                                            "config": {
                                                "dataScope": "tooltip_select_topic",
                                                "sortOrder": 10
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        </script>';

        return $html;
    }

    /**
     * @return mixed
     */
    public function getTopicCollection()
    {
        /* @var $collection Collection */
        $collection = $this->collectionFactory->create();
        $topicById = [];
        foreach ($collection as $topic) {
            $topicId = $topic->getId();
            $topicById[$topicId]['value']     = $topicId;
            $topicById[$topicId]['is_active'] = 1;
            $topicById[$topicId]['label']     = $topic->getName();
        }

        return $topicById;
    }

    /**
     * Get values for select
     *
     * @return array
     */
    public function getValues()
    {
        $values = $this->getValue();

        if (!is_array($values)) {
            $values = explode(',', $values);
        }

        if (empty($values)) {
            return [];
        }

        /* @var $collection Collection */
        $collection = $this->collectionFactory->create()->addIdFilter($values);

        $options = [];
        foreach ($collection as $topic) {
            $options[] = $topic->getId();
        }

        return $options;
    }
}
