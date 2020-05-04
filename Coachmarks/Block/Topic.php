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

namespace SM\Coachmarks\Block;

use Exception;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;
use SM\Coachmarks\Helper\Data as HelperData;
use SM\Coachmarks\Model\ResourceModel\Tooltip\CollectionFactory as TooltipCollectionFactory;
use SM\Coachmarks\Model\ResourceModel\Topic\CollectionFactory as TopicCollectionFactory;
use Magento\Framework\App\Request\Http;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Customer\Model\SessionFactory as CustomerSessionFactory;

/**
 * Class Topic
 *
 * @package SM\Coachmarks\Block
 */
class Topic extends Template
{
    const COACHMARKS_ATTR_DEFAULT_VALUE = '0';
    const PAGE_URL_CONTAIN = 'page_url';
    const CMS_HANDLE_NAME = 'page_cms';
    /**
     * @var HelperData
     */
    public $helperData;

    /**
     * @type StoreManagerInterface
     */
    protected $store;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var DateTime
     */
    protected $_date;

    /**
     * @var FilterProvider
     */
    public $filterProvider;

    /**
     * @var TopicCollectionFactory
     */
    public $_topicCollectionFactory;

    /**
     * @var TooltipCollectionFactory
     */
    public $_tooltipCollectionFactory;

    /**
     * @var Http
     */
    protected $_request;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var HttpContext
     */
    protected $_httpContext;

    /**
     * @var CustomerSessionFactory
     */
    protected $customerSession;

    /**
     * Topic constructor.
     *
     * @param Template\Context $context
     * @param HelperData $helperData
     * @param CustomerRepositoryInterface $customerRepository
     * @param DateTime $dateTime
     * @param FilterProvider $filterProvider
     * @param TopicCollectionFactory $topicCollectionFactory
     * @param TooltipCollectionFactory $tooltipCollectionFactory
     * @param Http $request
     * @param CustomerFactory $customerFactory
     * @param HttpContext $httpContext
     * @param CustomerSessionFactory $customerSession
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        HelperData $helperData,
        CustomerRepositoryInterface $customerRepository,
        DateTime $dateTime,
        FilterProvider $filterProvider,
        TopicCollectionFactory $topicCollectionFactory,
        TooltipCollectionFactory $tooltipCollectionFactory,
        Http $request,
        CustomerFactory $customerFactory,
        HttpContext $httpContext,
        CustomerSessionFactory $customerSession,
        array $data = []
    ) {
        $this->helperData = $helperData;
        $this->customerRepository = $customerRepository;
        $this->store = $context->getStoreManager();
        $this->_date = $dateTime;
        $this->filterProvider = $filterProvider;
        $this->_topicCollectionFactory = $topicCollectionFactory;
        $this->_tooltipCollectionFactory = $tooltipCollectionFactory;
        $this->_request = $request;
        $this->customerFactory = $customerFactory;
        $this->_httpContext = $httpContext;
        $this->customerSession = $customerSession;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setTemplate('SM_Coachmarks::coachmarks.phtml');
    }

    /**
     * @param $includepagesData
     *
     * @return bool
     */
    public function checkIncludePages($includepagesData)
    {
        $fullActionName = $this->getRequest()->getFullActionName();

        $arrayPages = explode("\n", $includepagesData);
        $includePages = array_map('trim', $arrayPages);

        return in_array($fullActionName, $includePages);
    }

    /**
     * @param $includePagesUrlData
     *
     * @return bool
     */
    public function checkIncludePaths($includePagesUrlData)
    {
        $currentPath = $this->getRequest()->getRequestUri();

        if ($includePagesUrlData) {
            $arrayPaths = explode("\n", $includePagesUrlData);
            $pathsUrl = array_map('trim', $arrayPaths);
            foreach ($pathsUrl as $path) {
                if (strpos($currentPath, $path) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function getCustomerLoggedIn()
    {
        return $this->_httpContext->getValue(CustomerContext::CONTEXT_AUTH);
    }

    /**
     * @param $customerId
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerById($customerId)
    {
        return $this->customerRepository->getById($customerId);
    }

    /**
     * @param $customerId
     *
     * @return mixed|string
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCoachmarksAttr($customerId)
    {
        $customer = $this->getCustomerById($customerId);
        $attribute = $customer->getCustomAttribute('coachmarks');
        if ($attribute && $attribute->getValue() != self::COACHMARKS_ATTR_DEFAULT_VALUE) {
            return $attribute->getValue();
        }

        return self::COACHMARKS_ATTR_DEFAULT_VALUE;
    }

    /**
     * @return int
     */
    public function getTopicId()
    {
        if ($this->getTopic()) {
            return $this->getTopic()->getTopicId();
        }

        return time();
    }

    /**
     * @param $content
     *
     * @return string
     * @throws Exception
     */
    public function getPageFilter($content)
    {
        return $this->filterProvider->getPageFilter()->filter($content);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getObjectTopicTooltipCollect()
    {
        #get topic collect
        $topicCollect = $this->_topicCollectionFactory->create()
            ->addFieldToFilter('status', 1)
            ->addOrder('sort_order');

        $topicCollect->getSelect()
            ->where(
                'FIND_IN_SET(0, store_ids) OR FIND_IN_SET(?, store_ids)',
                $this->_storeManager->getStore()->getId()
            )
            ->where(
                'page_url is null OR page_url LIKE ?',
                $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true])
            );

        #get tooltip collect
        $tooltipCollect = $this->_tooltipCollectionFactory->create()
            ->addFieldToFilter('status', 1)
            ->addOrder('sort_order');

        $data = new DataObject([
            'topicCollect' => $topicCollect->toArray(),
            'tooltipCollect' => $tooltipCollect->toArray()
        ]);

        return $this->helperData->jsonEncode($data);
    }

    /**
     * @param $topicId
     *
     * @return bool
     */
    public function isTopicHasTooltip($topicId)
    {
        #get topic collect
        $tooltipCollect = $this->_tooltipCollectionFactory->create()
            ->addFieldToFilter('status', 1)
            ->addFieldToFilter('topic_id', $topicId);

        if ($tooltipCollect->getSize() > 0) {
            return true;
        }

        return false;
    }

    /**
     * @return \SM\Coachmarks\Model\ResourceModel\Topic\Collection
     * @throws NoSuchEntityException
     */
    public function getTopicCollect()
    {
        #get topic collect
        $topicCollect = $this->_topicCollectionFactory->create()
            ->addFieldToFilter('status', 1)
            ->addOrder('sort_order', 'ASC');

        $topicCollect->getSelect()
            ->where(
                'FIND_IN_SET(0, store_ids) OR FIND_IN_SET(?, store_ids)',
                $this->_storeManager->getStore()->getId()
            );

        //remove topic has not tooltip or item tooltip is disabled
        foreach ($topicCollect as $key => $item) {
            if (!$this->isTopicHasTooltip($item->getTopicId())) {
                $topicCollect->removeItemByKey($key);
            }
            //check action type follow page url
            if ($item->getActionType() == self::PAGE_URL_CONTAIN && !$this->checkIncludePaths($item->getPageUrl())) {
                $topicCollect->removeItemByKey($key);
            }
            //check action type follow page cms
            if ($item->getActionType() == self::CMS_HANDLE_NAME && !$this->checkIncludePages($item->getPageCms())) {
                $topicCollect->removeItemByKey($key);
            }
        }

        return $topicCollect;
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getTopicCollectId()
    {
        $collectId = [];
        #get topic collect
        $topicCollect = $this->getTopicCollect();

        foreach ($topicCollect as $topic) {
            $collectId[] = $topic->getTopicId();
        }

        return $collectId;
    }

    /**
     * @return bool|string
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getObjectTopicCollect()
    {
        #get topic collect
        $topicCollect = $this->getTopicCollect();
        $coachedArray = [];
        /**check customer coachmarks visited*/
        if ($this->getCustomerLoggedIn()) {
            $customerId = $this->customerSession->create()->getCustomerId();
            $coachmarksVal = $this->getCoachmarksAttr($customerId);
            if ($coachmarksVal != self::COACHMARKS_ATTR_DEFAULT_VALUE) {
                $coachedArray = explode(',', $coachmarksVal);
            } else {
                $coachedArray = [self::COACHMARKS_ATTR_DEFAULT_VALUE];
            }

            //filter coachmarks has coached
            $hasTopicAllow = 0;
            foreach ($topicCollect as $key => $value) {
                foreach ($coachedArray as $topic) {
                    if (strcmp($value->getTopicId(), $topic) == 0) {
                        $topicCollect->removeItemByKey($key);
                    }
                }
            }
            //return coachmarks not visited yet
            foreach ($topicCollect as $key => $value) {
                if ($value->getTopicId()) {
                    $hasTopicAllow = $hasTopicAllow + 1;
                }
            }
            if ($hasTopicAllow > 0) {
                $data = new DataObject([
                    'topicCollect' => $topicCollect->toArray()
                ]);

                return $this->helperData->jsonEncode($data);
            }
        }

        return false;
    }

    /**
     * @return bool|string
     * @throws NoSuchEntityException
     */
    public function getObjectTooltipCollect()
    {
        #get tooltip collect
        $topicCollectId = $this->getTopicCollectId();
        $tooltipCollect = $this->_tooltipCollectionFactory->create()
            ->addFieldToFilter('status', 1)
            ->addFieldToFilter('topic_id', ['in' => $topicCollectId])
            ->addOrder('sort_order', 'ASC');

        if ($tooltipCollect->getSize() > 0) {
            $data = new DataObject([
                'tooltipCollect' => $tooltipCollect->toArray()
            ]);

            return $this->helperData->jsonEncode($data);
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->helperData->isEnabled();
    }
}
