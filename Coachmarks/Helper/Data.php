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

namespace SM\Coachmarks\Helper;

use Exception;
use Magento\Backend\App\Config;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use SM\Coachmarks\Model\TooltipFactory;
use SM\Coachmarks\Model\ResourceModel\Tooltip\Collection;
use SM\Coachmarks\Model\TopicFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Data
 *
 * @package SM\Coachmarks\Helper
 */
class Data extends AbstractHelper
{
    const CONFIG_MODULE_PATH = 'coachmarks';
    /**
     * @var TooltipFactory
     */
    public $tooltipFactory;
    /**
     * @var TopicFactory
     */
    public $topicFactory;
    /**
     * @var DateTime
     */
    protected $date;
    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * @var JsonHelper
     */
    protected $_jsonHelper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @type ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var Config
     */
    protected $backendConfig;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Data constructor.
     *
     * @param DateTime $date
     * @param Context $context
     * @param HttpContext $httpContext
     * @param TooltipFactory $tooltipFactory
     * @param TopicFactory $topicFactory
     * @param StoreManagerInterface $storeManager
     * @param JsonHelper $jsonHelper
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        DateTime $date,
        Context $context,
        HttpContext $httpContext,
        TooltipFactory $tooltipFactory,
        TopicFactory $topicFactory,
        StoreManagerInterface $storeManager,
        JsonHelper $jsonHelper,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->date = $date;
        $this->httpContext = $httpContext;
        $this->tooltipFactory = $tooltipFactory;
        $this->topicFactory = $topicFactory;
        $this->_jsonHelper = $jsonHelper;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;

        parent::__construct($context);
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE;

        return $this->scopeConfig->getValue("coachmarks/general/enabled", $storeScope);
    }

    /**
     * Encode the mixed $valueToEncode into the JSON format
     *
     * @param mixed $valueToEncode
     *
     * @return string
     */
    public function jsonEncode($valueToEncode)
    {
        try {
            $encodeValue = $this->_jsonHelper->jsonEncode($valueToEncode);
        } catch (Exception $e) {
            $encodeValue = '{}';
        }

        return $encodeValue;
    }

    /**
     * @return mixed
     */
    protected function getSerializeClass()
    {
        return $this->objectManager->get('Zend_Serializer_Adapter_PhpSerialize');
    }

    /**
     * @param $data
     *
     * @return string
     */
    public function serialize($data)
    {
        return $this->getSerializeClass()->serialize($data);
    }

    /**
     * @param $string
     *
     * @return mixed
     */
    public function unserialize($string)
    {
        return $this->getSerializeClass()->unserialize($string);
    }

    /**
     * @param $id
     *
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getTooltipCollection($id)
    {
        return $this->tooltipFactory->create()->getCollection()->addFieldToFilter('tooltip_id', $id);
    }

    /**
     * @param $id
     *
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getCountTooltipAsigned($id)
    {
        return $this->tooltipFactory->create()->getCollection()->addFieldToFilter('topic_id', $id);
    }

    /**
     * @param $id
     *
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getTopicCollection($id)
    {
        return $this->topicFactory->create()->getCollection()
            ->addFieldToFilter('topic_id', $id);
    }

    /**
     * @return \SM\Coachmarks\Model\ResourceModel\Topic\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getActiveTopics()
    {
        /** @var \SM\Coachmarks\Model\ResourceModel\Topic\Collection $collection */
        $collection = $this->topicFactory->create()
            ->getCollection()
            ->addFieldToFilter('status', 1)
            ->addOrder('sort_order');

        $collection->getSelect()
            ->where('FIND_IN_SET(0, store_ids) OR FIND_IN_SET(?, store_ids)', $this->storeManager->getStore()->getId())
            ->where('from_date is null OR from_date <= ?', $this->date->date())
            ->where('to_date is null OR to_date >= ?', $this->date->date());

        return $collection;
    }
}
