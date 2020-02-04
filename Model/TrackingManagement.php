<?php

namespace Monogo\TrackingNumber\Model;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Shipment\Track;
use Monogo\TrackingNumber\Api\TrackingManagementInterface;
use Monogo\TrackingNumber\Model\Config\TrackingConfig;

class TrackingManagement implements TrackingManagementInterface
{
    /**
     * @var TrackingConfig
     */
    private $config;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param TrackingConfig $trackingConfig
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(TrackingConfig $trackingConfig, OrderRepositoryInterface $orderRepository)
    {
        $this->config = $trackingConfig;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @inheritDoc
     */
    public function getTrackingLinks($orderId)
    {
        $order = $this->orderRepository->get($orderId);

        return $this->getTrackMappings($order);
    }

    /**
     * @param OrderInterface $order
     * @return array
     */
    public function getTrackMappings($order)
    {
        $mappings = [];
        $shippingMethod = $order->getShippingMethod();
        $trackCollection = $order->getTracksCollection();
        $mappingUrls = $this->config->getMappingUrls();
        if (array_key_exists($shippingMethod, $mappingUrls)) {
            /** @var Track $track */
            foreach ($trackCollection as $track) {
                $mappings[] = [
                    'title' => $track->getTitle(),
                    'number' => $track->getNumber(),
                    'url' => str_replace($this->config->getSign(), $track->getNumber(), $mappingUrls[$shippingMethod])
                ];
            }
        }
        return $mappings;
    }
}
