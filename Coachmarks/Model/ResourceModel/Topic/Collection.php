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

namespace SM\Coachmarks\Model\ResourceModel\Topic;

use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Zend_Db_Select;

/**
 * Class Collection
 *
 * @package SM\Coachmarks\Model\ResourceModel\Topic
 */
class Collection extends AbstractCollection
{
    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'topic_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'sm_coachmarks_topic_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'topic_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('SM\Coachmarks\Model\Topic', 'SM\Coachmarks\Model\ResourceModel\Topic');
    }

    /**
     * @param $topicIds
     *
     * @return $this
     */
    public function addIdFilter($topicIds)
    {
        $condition = '';

        if (is_array($topicIds)) {
            if (!empty($topicIds)) {
                $condition = ['in' => $topicIds];
            }
        } elseif (is_numeric($topicIds)) {
            $condition = $topicIds;
        } elseif (is_string($topicIds)) {
            $ids = explode(',', $topicIds);
            if (empty($ids)) {
                $condition = $topicIds;
            } else {
                $condition = ['in' => $ids];
            }
        }

        if ($condition !== '') {
            $this->addFieldToFilter('topic_id', $condition);
        }

        return $this;
    }

    /**
     * @param $customerGroup
     * @param $storeId
     *
     * @return $this
     */
    public function addActiveFilter($customerGroup = null, $storeId = null)
    {
        $this->addFieldToFilter('status', true)->setOrder('sort_order', Select::SQL_ASC);

        if (isset($storeId)) {
            $this->getSelect()
                ->where('FIND_IN_SET(0, store_ids) OR FIND_IN_SET(?, store_ids)', $storeId);
        }

        return $this;
    }
}
