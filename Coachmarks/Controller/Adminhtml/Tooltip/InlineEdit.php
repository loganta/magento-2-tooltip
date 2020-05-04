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

namespace SM\Coachmarks\Controller\Adminhtml\Tooltip;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use SM\Coachmarks\Model\Tooltip;
use SM\Coachmarks\Model\TooltipFactory;
use RuntimeException;

/**
 * Class InlineEdit
 *
 * @package SM\Coachmarks\Controller\Adminhtml\Tooltip
 */
class InlineEdit extends Action
{
    /**
     * JSON Factory
     *
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var TooltipFactory
     */
    protected $tooltipFactory;

    /**
     * InlineEdit constructor.
     *
     * @param JsonFactory $jsonFactory
     * @param TooltipFactory $tooltipFactory
     * @param Context $context
     */
    public function __construct(
        JsonFactory $jsonFactory,
        TooltipFactory $tooltipFactory,
        Context $context
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->tooltipFactory = $tooltipFactory;

        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
        $postItems = $this->getRequest()->getParam('items', []);
        if (!(!empty($postItems) && $this->getRequest()->getParam('isAjax'))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error'    => true,
            ]);
        }
        foreach (array_keys($postItems) as $tooltipId) {
            /** @var Tooltip $tooltip */
            $tooltip = $this->tooltipFactory->create()->load($tooltipId);
            try {
                $tooltipData = $postItems[$tooltipId];//todo: handle dates
                $tooltip->addData($tooltipData);
                $tooltip->save();
            } catch (RuntimeException $e) {
                $messages[] = $this->getErrorWithTooltipId($tooltip, $e->getMessage());
                $error = true;
            } catch (Exception $e) {
                $messages[] = $this->getErrorWithTooltipId(
                    $tooltip,
                    __('Something went wrong while saving the Tooltip.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error'    => $error
        ]);
    }

    /**
     * @param Tooltip $tooltip
     * @param $errorText
     *
     * @return string
     */
    protected function getErrorWithTooltipId(Tooltip $tooltip, $errorText)
    {
        return '[Tooltip ID: ' . $tooltip->getId() . '] ' . $errorText;
    }
}
