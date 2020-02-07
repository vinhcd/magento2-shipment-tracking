<?php

namespace Monogo\TrackingNumber\Model;

use Magento\Framework\Data\Collection;
use Magento\Framework\Data\CollectionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Shipment\Track;
use Monogo\TrackingNumber\Api\TrackingManagementInterface;
use Monogo\TrackingNumber\Model\Config\TrackingConfig;
use Monogo\TrackingNumber\Model\TrackingDataFactory;

class TrackingManagement implements TrackingManagementInterface
{
    /**
     * @var TrackingConfig
     */
    protected $config;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var TrackingDataFactory
     *
     */
    protected $trackingDataFactory;

    /**
     * @param TrackingConfig $trackingConfig
     * @param OrderRepositoryInterface $orderRepository
     * @param CollectionFactory $collectionFactory
     * @param TrackingDataFactory $trackingDataFactory
     */
    public function __construct(
        TrackingConfig $trackingConfig,
        OrderRepositoryInterface $orderRepository,
        CollectionFactory $collectionFactory,
        TrackingDataFactory $trackingDataFactory
    )
    {
        $this->config = $trackingConfig;
        $this->orderRepository = $orderRepository;
        $this->collectionFactory = $collectionFactory;
        $this->trackingDataFactory = $trackingDataFactory;
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
                $trackItem = $this->trackingDataFactory->create();
                $trackItem->setTitle($track->getTitle());
                $trackItem->setNumber($track->getNumber());
                $trackItem->setUrl(str_replace($this->config->getSign(), $track->getNumber(), $mappingUrls[$shippingMethod]));

                $result->addItem($trackItem);
            }
        }
        return $result;
    }
}
