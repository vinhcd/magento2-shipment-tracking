<?php

namespace Monogo\TrackingNumber\Observer;

use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Monogo\TrackingNumber\Helper\OrderTrackingHelper;

class AddTrackingToEmail implements ObserverInterface
{
    /**
     * @var OrderTrackingHelper
     */
    protected $trackingHelper;

    /**
     * AddTrackingToEmail constructor.
     * @param OrderTrackingHelper $trackingHelper
     */
    public function __construct(OrderTrackingHelper $trackingHelper)
    {
        $this->trackingHelper = $trackingHelper;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        if (!$this->trackingHelper->isConfigEnabled()) {
            return;
        }
        /** @var DataObject $transport */
        $transport = $observer->getData('transportObject');
        /** @var Order $order */
        $order = $transport->getData('order');

        if ($order && count($this->trackingHelper->getTrackingNumbersAndUrls($order)) > 0) {
            $transport->setData('has_tracking_links', true);
            $transport->setData('tracking_links', $this->trackingHelper->generateTrackingHtml($order));
        }
    }
}
