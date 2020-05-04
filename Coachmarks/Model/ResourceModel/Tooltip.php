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

/**
 * Class Tooltip
 *
 * @package SM\Coachmarks\Model\ResourceModel
 */
class Tooltip extends AbstractDb
{
    /**
     * Date model
     *
     * @var DateTime
     */
    protected $date;

    /**
     * Topic relation model
     *
     * @var string
     */
    protected $tooltipTopicTable;

    /**
     * Event Manager
     *
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * constructor
     *
     * @param DateTime $date
     * @param ManagerInterface $eventManager
     * @param Context $context
     */
    public function __construct(
        DateTime $date,
        ManagerInterface $eventManager,
        Context $context
    ) {
        $this->date = $date;
        $this->eventManager = $eventManager;

        parent::__construct($context);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sm_coachmarks_tooltip', 'tooltip_id');
    }

    /**
     * @param $id
     *
     * @return string
     * @throws LocalizedException
     */
    public function getTooltipNameById($id)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'name')
            ->where('tooltip_id = :tooltip_id');
        $binds = ['tooltip_id' => (int)$id];

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * @param AbstractModel $object
     *
     * @return $this|AbstractDb
     */
    protected function _beforeSave(AbstractModel $object)
    {
        //set default Update At and Create At time post
        $object->setUpdatedAt($this->date->date());
        if ($object->isObjectNew()) {
            $object->setCreatedAt($this->date->date());
        }

        return $this;
    }

    /**
     * @param \SM\Coachmarks\Model\Tooltip $tooltip
     *
     * @return array
     */
    public function getTopicIds(\SM\Coachmarks\Model\Tooltip $tooltip)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->tooltipTopicTable, 'topic_id')
            ->where('tooltip_id = ?', (int)$tooltip->getId());

        return $adapter->fetchCol($select);
    }
}
