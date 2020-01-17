<?php

namespace Monogo\TrackingNumber\Model\Config\Source;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Exception\LocalizedException;
use Monogo\TrackingNumber\Block\Adminhtml\Form\Field\TrackingUrlsFormField;

class TrackingUrlsRenderer extends AbstractFieldArray
{
    /**
     * @var TrackingUrlsFormField
     */
    protected $renderer;

    /**
     * @return void
     * @throws LocalizedException
     */
    protected function _prepareToRender()
    {
        $this->addColumn('method',
            [
                'label' => __('Shipping method'),
                'class' => 'required-entry',
                'renderer' => $this->getCarriersRenderer()
            ]
        );
        $this->addColumn('url',['label' => __('Tracking URL'), 'class' => 'required-entry']);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Mapping');
    }

    /**
     * @throws LocalizedException
     * @return TrackingUrlsFormField
     */
    protected function getCarriersRenderer()
    {
        if (!$this->renderer) {
            $this->renderer = $this->getLayout()->createBlock(
                TrackingUrlsFormField::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->renderer;
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $options = [];
        $carrierAttribute = $row->getData('method');

        $key = 'option_' . $this->getCarriersRenderer()->calcOptionHash($carrierAttribute);
        $options[$key] = 'selected="selected"';
        $row->setData('option_extra_attrs', $options);
    }
}
