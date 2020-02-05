<?php

namespace Monogo\TrackingNumber\Api\Data;

interface TrackingDataInterface
{
    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getNumber();

    /**
     * @param string $number
     * @return $this
     */
    public function setNumber($number);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl($url);

}
