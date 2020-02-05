<?php

namespace Monogo\TrackingNumber\Api;

interface TrackingManagementInterface
{
    /**
     * @param int $orderId
     * @return \Monogo\TrackingNumber\Api\Data\GetTrackingDataInterface
     * @throws \Exception
     */
    public function getTrackingLinks($orderId);
}
