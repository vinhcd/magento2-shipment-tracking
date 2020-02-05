<?php

namespace Monogo\TrackingNumber\Model;

use Magento\Framework\Data\Collection;
use Magento\Framework\Data\CollectionFactory;
use Magento\Framework\DataObject;
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
     * @var \Monogo\TrackingNumber\Model\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param TrackingConfig $trackingConfig
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        TrackingConfig $trackingConfig,
        OrderRepositoryInterface $orderRepository,
        CollectionFactory $collectionFactory
    )
    {
        $this->config = $trackingConfig;
        $this->orderRepository = $orderRepository;
        $this->collectionFactory = $collectionFactory;
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
     * @param $order
     * @return Collection
     * @throws \Exception
     */
    public function getTrackMappings($order)
    {
        $shippingMethod = $order->getShippingMethod();
        $trackCollection = $order->getTracksCollection();
        $mappingUrls = $this->config->getMappingUrls();
        $result = $this->collectionFactory->create();
        if (array_key_exists($shippingMethod, $mappingUrls)) {
            /** @var Track $track */
            foreach ($trackCollection as $track) {
                $trackItem = new DataObject();
                $trackItem->setTitle($track->getTitle());
                $trackItem->setNumber($track->getNumber());
                $trackItem->setUrl(str_replace($this->config->getSign(), $track->getNumber(), $mappingUrls[$shippingMethod]));

                $result->addItem($trackItem);
            }
        }
        return $result;
    }
}
