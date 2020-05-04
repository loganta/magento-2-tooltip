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
use SM\Coachmarks\Model\ResourceModel\Tooltip\Collection;
use SM\Coachmarks\Model\ResourceModel\Tooltip\CollectionFactory;

/**
 * Class Topic
 *
 * @package SM\Coachmarks\Model
 */
class Topic extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'sm_coachmarks_topic';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'sm_coachmarks_topic';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'sm_coachmarks_topic';

    /**
     * Tooltip Collection
     *
     * @var Collection
     */
    protected $tooltipCollection;

    /**
     * Tooltip Collection Factory
     *
     * @var CollectionFactory
     */
    protected $tooltipCollectionFactory;

    /**
     * Topic constructor.
     *
     * @param CollectionFactory $tooltipCollectionFactory
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        CollectionFactory $tooltipCollectionFactory,
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->tooltipCollectionFactory = $tooltipCollectionFactory;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('SM\Coachmarks\Model\ResourceModel\Topic');
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
        $values = [];
        $values['status'] = '1';

        return $values;
    }

    /**
     * @return array|mixed
     */
    public function getTooltipsPosition()
    {
        if (!$this->getId()) {
            return [];
        }

        $array = $this->getData('tooltips_position');
        if ($array === null) {
            $array = $this->getResource()->getTooltipsPosition($this);
            $this->setData('tooltips_position', $array);
        }

        return $array;
    }

    /**
     * @return Collection
     */
    public function getSelectedTooltipsCollection()
    {
        if ($this->tooltipCollection === null) {
            $collection = $this->tooltipCollectionFactory->create();
            $this->tooltipCollection = $collection;
        }

        return $this->tooltipCollection;
    }
}
