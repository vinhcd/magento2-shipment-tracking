<?php

namespace Monogo\TrackingNumber\Block\Tracking;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipment;
use Magento\Shipping\Model\Order\Track;
use Magento\Shipping\Model\Order\TrackFactory;
use Monogo\TrackingNumber\Helper\OrderTrackingHelper;
use Psr\Log\LoggerInterface;

class Detail extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var OrderTrackingHelper
     */
    protected $orderTrackingHelper;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var ShipmentRepositoryInterface
     */
    protected $shipmentRepository;

    /**
     * @var TrackFactory
     */
    protected $trackFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Order|OrderInterface
     */
    protected $order;

    /**
     * Detail constructor.
     * @param Template\Context $context
     * @param Registry $registry
     * @param OrderRepositoryInterface $orderRepository
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param TrackFactory $trackFactory
     * @param OrderTrackingHelper $orderTrackingHelper
     * @param LoggerInterface $logger
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        TrackFactory $trackFactory,
        OrderTrackingHelper $orderTrackingHelper,
        LoggerInterface $logger,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->orderRepository = $orderRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->trackFactory = $trackFactory;
        $this->orderTrackingHelper = $orderTrackingHelper;
        $this->logger = $logger;

        parent::__construct($context, $data);
    }

    /**
     * @param string $trackingNumber
     * @return string
     */
    public function getTrackingUrl($trackingNumber)
    {
        if ($this->getOrder() && $this->orderTrackingHelper->isConfigEnabled()) {
            return $this->orderTrackingHelper->getTrackingUrl($this->getOrder(), $trackingNumber);
        }
        return '';
    }

    /**
     * @return Order|OrderInterface|null
     */
    public function getOrder()
    {
        if (!$this->order) {
            try {
                /* @var $info \Magento\Shipping\Model\Info */
                $info = $this->registry->registry('current_shipping_info');
                if ($info->getOrderId()) {
                    $this->order = $this->orderRepository->get($info->getOrderId());
                } elseif ($info->getShipId()) {
                    /* @var $shipment Shipment */
                    $shipment = $this->shipmentRepository->get($info->getShipId());
                    $this->order = $shipment->getOrder();
                } elseif ($info->getTrackId()) {
                    /** @var Track $track */
                    $track = $this->trackFactory->create()->load($info->getTrackId());
                    $this->order = $this->orderRepository->get($track->getOrderId());
                }
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
        return $this->order;
    }
}
