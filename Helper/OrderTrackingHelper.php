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
     * @var array
     */
    protected $orderLinks = [];

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
     * @return TrackingConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return bool
     */
    public function isConfigEnabled()
    {
        return $this->getConfig()->isEnabled();
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
    public function getTrackingNumbersAndUrls($order)
    {
        $id = $order->getId();
        if (array_key_exists($id, $this->orderLinks)) {
            return  $this->orderLinks[$id];
        }
        $links = [];

        $shippingMethod = $order->getShippingMethod();
        $trackCollection = $order->getTracksCollection();
        $mappingUrls = $this->config->getMappingUrls();
        if (array_key_exists($shippingMethod, $mappingUrls)) {
            /** @var Track $track */
            foreach ($trackCollection as $track) {
                $links[] = ['number' => $track->getNumber(), 'url' => str_replace($this->getSign(), $track->getNumber(), $mappingUrls[$shippingMethod])];
            }
        }
        $this->orderLinks[$id] = $links;

        return $links;
    }

    /**
     * @param Order|OrderInterface $order
     * @return string
     */
    public function generateTrackingHtml($order)
    {
        $html = '';
        $links = $this->getTrackingNumbersAndUrls($order);
        foreach ($links as $link) {
            $html .= '<a href="' . $link['url'] . '" target="_blank">' . $link['number'] . '</a><br/>';
        }
        return $html;
    }

    /**
     * @return string
     */
    protected function getSign()
    {
        return $this->sign;
    }
}
