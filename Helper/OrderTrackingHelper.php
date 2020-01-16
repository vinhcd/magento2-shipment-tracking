<?php

namespace Monogo\TrackingNumber\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipment\Track;
use Monogo\TrackingNumber\Model\Config\TrackingConfig;

class OrderTrackingHelper extends AbstractHelper
{
    /**
     * @var string
     */
    protected $sign = '$tracking_number$';

    /**
     * @var TrackingConfig
     */
    protected $config;

    /**
     * OrderTracking constructor.
     * @param Context $context
     * @param TrackingConfig $config
     */
    public function __construct(Context $context, TrackingConfig $config)
    {
        $this->config = $config;

        parent::__construct($context);
    }

    /**
     * @param Order $order
     * @param string $trackingNumber
     * @return string
     */
    public function getTrackingUrl($order, $trackingNumber)
    {
        $shippingMethod = $order->getShippingMethod();
        $mappingUrls = $this->config->getMappingUrls();

        if (array_key_exists($shippingMethod, $mappingUrls)) {
            return str_replace($this->getSign(), $trackingNumber, $mappingUrls[$shippingMethod]);
        }
        return '';
    }

    /**
     * @param Order|OrderInterface $order
     * @return array
     */
    public function getTrackingUrls($order)
    {
        $links = [];

        $shippingMethod = $order->getShippingMethod();
        $trackCollection = $order->getTracksCollection();
        $mappingUrls = $this->config->getMappingUrls();

        if (array_key_exists($shippingMethod, $mappingUrls)) {
            /** @var Track $track */
            foreach ($trackCollection as $track) {
                $links[] = str_replace($this->getSign(), $track->getNumber(), $mappingUrls[$shippingMethod]);
            }
        }
        return $links;
    }

    /**
     * @return string
     */
    protected function getSign()
    {
        return $this->sign;
    }
}
