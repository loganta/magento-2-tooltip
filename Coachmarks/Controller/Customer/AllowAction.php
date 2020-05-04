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

namespace SM\Coachmarks\Controller\Customer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\ResourceModel\CustomerFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Customer\Model\Customer;

/**
 * Class AllowAction
 *
 * @package SM\Coachmarks\Controller\Customer
 */
class AllowAction extends Action
{
    const COACHMARKS_ATTR_DEFAULT_VALUE = '0';
    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var CustomerFactory
     */
    protected $customer;

    /**
     * @var HttpContext
     */
    protected $_httpContext;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * AllowAction constructor.
     *
     * @param Context $context
     * @param ResultFactory $resultFactory
     * @param Session $customerSession
     * @param CustomerFactory $customerFactory
     * @param HttpContext $httpContext
     * @param Customer $customer
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Context $context,
        ResultFactory $resultFactory,
        Session $customerSession,
        CustomerFactory $customerFactory,
        HttpContext $httpContext,
        Customer $customer,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->resultFactory = $resultFactory;
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
        $this->_httpContext = $httpContext;
        $this->customer = $customer;
        $this->customerRepository = $customerRepository;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $data = ['response' => true];
        $topicId = $this->getRequest()->getParam('topicId');
        if ($topicId != '' && $this->getCustomerLoggedIn()) {
            //save customer attribute coachmarks
            $customerId = $this->customerSession->getCustomerId();
            $attributeVal = $this->getCoachmarksAttr($customerId);
            //process value to save
            $attrValArray = explode(',', $attributeVal);
            foreach ($attrValArray as $topicCoached) {
                if (strcmp($topicId, $topicCoached) == 0) {
                    $data = ['response' => false];
                }
            }
        }
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($data);

        return $resultJson;
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
     * @return mixed|string
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCoachmarksAttr($customerId)
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException $noSuchEntityException) {
            return self::COACHMARKS_ATTR_DEFAULT_VALUE;
        }
        $attribute = $customer->getCustomAttribute('coachmarks');
        if ($attribute && $attribute->getValue() != self::COACHMARKS_ATTR_DEFAULT_VALUE) {
            return $attribute->getValue();
        }

        return self::COACHMARKS_ATTR_DEFAULT_VALUE;
    }
}
