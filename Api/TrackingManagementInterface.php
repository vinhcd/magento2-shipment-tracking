<?php

namespace Monogo\TrackingNumber\Api;

interface TrackingManagementInterface
{
    /**
     * @param int $orderId
     * @return array
     * @throws \Exception
     */
    public function getTrackingLinks($orderId);
}
