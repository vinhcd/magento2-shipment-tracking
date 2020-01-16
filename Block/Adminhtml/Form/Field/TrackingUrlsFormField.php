<?php

namespace Monogo\TrackingNumber\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Context;
use Magento\Shipping\Model\Config;

class TrackingUrlsFormField extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * @var Config
     */
    protected $shippingConfig;

    /**
     * Activation constructor.
     *
     * @param Context $context
     * @param Config $shippingConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $shippingConfig,
        array $data = []
    ) {
        $this->shippingConfig = $shippingConfig;

        parent::__construct($context, $data);
    }

    /**
     * @see \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray::renderCellTemplate
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $carriers = $this->shippingConfig->getAllCarriers();
            foreach ($carriers as $carrierCode => $carrierModel) {
                $display = !$carrierModel->isActive() ? ['style' => 'display:none'] : [];
                $carrierTitle = $this->_scopeConfig->getValue(
                    'carriers/' . $carrierCode . '/title',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                ) ?: $carrierCode;
                $carrierMethods = $carrierModel->getAllowedMethods();
                if (!$carrierMethods) {
                    continue;
                }
                foreach ($carrierMethods as $methodCode => $methodTitle) {
                    $methodTitle = $methodTitle ?: $methodCode;
                    $this->addOption($carrierCode . '_' . $methodCode, $carrierTitle . ' - ' . $methodTitle, $display);
                }
            }
        }
        return parent::_toHtml();
    }
}
