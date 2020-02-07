<?php

namespace Monogo\TrackingNumber\Model;

use Magento\Framework\DataObject;
use Monogo\TrackingNumber\Api\Data\TrackingDataInterface;

class TrackingData extends DataObject implements TrackingDataInterface
{
    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->_getData('title');
    }

    /**
     * @inheritDoc
     */
    public function setTitle($title)
    {
        return $this->setData('title', $title);
    }

    /**
     * @inheritDoc
     */
    public function getNumber()
    {
        return $this->_getData('number');
    }

    /**
     * @inheritDoc
     */
    public function setNumber($number)
    {
        return $this->setData('number', $number);
    }

    /**
     * @inheritDoc
     */
    public function getUrl()
    {
        return $this->_getData('url');
    }

    /**
     * @inheritDoc
     */
    public function setUrl($url)
    {
        return $this->setData('url', $url);
    }
}
