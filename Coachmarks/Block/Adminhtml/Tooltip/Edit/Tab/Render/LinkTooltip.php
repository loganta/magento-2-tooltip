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

namespace SM\Coachmarks\Block\Adminhtml\Tooltip\Edit\Tab\Render;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Magento\Framework\UrlInterface;

/**
 * Class LinkTooltip
 *
 * @package SM\Coachmarks\Block\Adminhtml\Tooltip\Edit\Tab\Render
 */
class LinkTooltip extends AbstractRenderer
{
    /**
     * Url path  to edit
     *
     * @var string
     */
    const URL_PATH_EDIT = 'coachmarks/tooltip/edit';

    /**
     * URL builder
     *
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * LinkTooltip constructor.
     *
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param DataObject $row
     *
     * @return string
     */
    public function render(DataObject $row)
    {
        $tooltipId = $row->getData($this->getColumn()->getIndex());
        $url = $this->urlBuilder->getUrl(static::URL_PATH_EDIT, ['tooltip_id' => $tooltipId]);

        return '<a href=' . $url . ' target="_blank">' . __('Edit') . '</a>';
    }
}
