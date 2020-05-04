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

namespace SM\Coachmarks\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use SM\Coachmarks\Model\ResourceModel\Topic\Collection;
use SM\Coachmarks\Model\ResourceModel\Topic\CollectionFactory as TopicCollectionFactory;
use SM\Coachmarks\Model\ResourceModel\Tooltip as ResourceTooltip;

/**
 * Class Tooltip
 *
 * @package SM\Coachmarks\Model
 */
class Tooltip extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'sm_coachmarks_tooltip';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'sm_coachmarks_tooltip';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'sm_coachmarks_tooltip';

    /**
     * Topic Collection
     *
     * @var Collection
     */
    protected $topicCollection;

    /**
     * @var TopicCollectionFactory
     */
    protected $topicCollectionFactory;

    /**
     * Tooltip constructor.
     *
     * @param TopicCollectionFactory $topicCollectionFactory
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        TopicCollectionFactory $topicCollectionFactory,
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->topicCollectionFactory = $topicCollectionFactory;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceTooltip::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * get entity default values
     *
     * @return array
     */
    public function getDefaultValues()
    {
        return ['status => 1', 'type' => '0'];
    }

    /**
     * @return Collection
     */
    public function getSelectedTopicsCollection()
    {
        if ($this->topicCollection === null) {
            /** @var \SM\Coachmarks\Model\ResourceModel\Topic\Collection $collection */
            $collection = $this->topicCollectionFactory->create();
            $collection->addFieldToFilter('status', 1);

            $this->topicCollection = $collection;
        }

        return $this->topicCollection;
    }

    /**
     * @return array
     */
    public function getTopicIds()
    {
        if (!$this->hasData('topic_ids')) {
            $ids = $this->getResource()->getTopicIds($this);

            $this->setData('topic_ids', $ids);
        }

        return (array)$this->getData('topic_ids');
    }
}
