<?php

namespace Monogo\TrackingNumber\Observer;

use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Monogo\TrackingNumber\Helper\OrderTrackingHelper;

class AddTrackingToEmail implements \Magento\Framework\Event\ObserverInterface
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
        $sender = $observer->getData('sender');
        if (!$this->trackingHelper->isConfigEnabled() || !$sender instanceof OrderSender) {
            return;
        }
        /** @var DataObject $transport */
        $transport = $observer->getData('transportObject');
        /** @var Order $order */
        $order = $transport->getData('order');

        if (count($this->trackingHelper->getTrackingNumbersAndUrls($order)) > 0) {
            $transport->setData('has_tracking_links', true);
            $transport->setData('tracking_links', $this->trackingHelper->generateTrackingHtml($order));
        }
    }
}
