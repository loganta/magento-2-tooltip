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

namespace SM\Coachmarks\Model\ResourceModel;

use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use SM\Coachmarks\Helper\Data as HelperData;
use Zend_Serializer_Exception;

/**
 * Class Topic
 *
 * @package SM\Coachmarks\Model\ResourceModel
 */
class Topic extends AbstractDb
{
    /**
     * Date model
     *
     * @var DateTime
     */
    protected $date;

    /**
     * Tooltip relation model
     *
     * @var string
     */
    protected $topicTooltipTable;

    /**
     * Event Manager
     *
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * Topic constructor.
     *
     * @param DateTime $date
     * @param ManagerInterface $eventManager
     * @param Context $context
     * @param HelperData $helperData
     */
    public function __construct(
        DateTime $date,
        ManagerInterface $eventManager,
        Context $context,
        HelperData $helperData
    ) {
        $this->date = $date;
        $this->eventManager = $eventManager;
        $this->helperData = $helperData;

        parent::__construct($context);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sm_coachmarks_topic', 'topic_id');
    }

    /**
     * Retrieves Topic Name from DB by passed id.
     *
     * @param $id
     *
     * @return string
     * @throws LocalizedException
     */
    public function getTopicNameById($id)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'name')
            ->where('topic_id = :topic_id');
        $binds = ['topic_id' => (int)$id];

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * before save callback
     *
     * @param AbstractModel $object
     *
     * @return AbstractDb
     * @throws Zend_Serializer_Exception
     */
    protected function _beforeSave(AbstractModel $object)
    {
        //set default Update At and Create At time post
        $object->setUpdatedAt($this->date->date());
        if ($object->isObjectNew()) {
            $object->setCreatedAt($this->date->date());
        }

        $storeIds = $object->getStoreIds();
        if (is_array($storeIds)) {
            $object->setStoreIds(implode(',', $storeIds));
        }

        return parent::_beforeSave($object);
    }

    /**
     * @param \SM\Coachmarks\Model\Topic $topic
     *
     * @return array
     */
    public function getTooltipsPosition(\SM\Coachmarks\Model\Topic $topic)
    {
        $select = $this->getConnection()->select()->from(
            $this->topicTooltipTable,
            ['tooltip_id', 'position']
        )
            ->where(
                'topic_id = :topic_id'
            );
        $bind = ['topic_id' => (int)$topic->getId()];

        return $this->getConnection()->fetchPairs($select, $bind);
    }
}
