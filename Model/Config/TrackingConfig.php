<?php

namespace Monogo\TrackingNumber\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;

class TrackingConfig
{
    const ENABLED = 'shipping/tracking_number/enabled';

    const INCLUDE_IN_CUSTOMER_DSB = 'shipping/tracking_number/include_in_customer_dashboard';

    const MAPPING_URLS = 'shipping/tracking_number/mapping_urls';

    /**
     * @var ScopeConfigInterface
     */
    protected $config;

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * TrackingConfig constructor.
     * @param ScopeConfigInterface $config
     * @param Json $serializer
     * @param LoggerInterface $logger
     */
    public function __construct(ScopeConfigInterface $config, Json $serializer, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->config->getValue(self::ENABLED) === 1;
    }

    /**
     * @return bool
     */
    public function includeInCustomerDashboard()
    {
        return $this->config->getValue(self::INCLUDE_IN_CUSTOMER_DSB) === 1;
    }

    /**
     * @return array
     */
    public function getMappingUrls()
    {
        $result = [];
        $serializedMapping = $this->config->getValue(self::MAPPING_URLS);
        if ($serializedMapping) {
            try {
                $mappings = $this->serializer->unserialize($serializedMapping);
                foreach ($mappings as $mapping) {
                    $result[$mapping['method']] = $mapping['url'];
                }
            } catch (\Exception $e) {
                $this->logger->critical(
                    sprintf(
                        'Failed to unserialize %s config value. The error is: %s',
                        self::MAPPING_URLS,
                        $e->getMessage()
                    )
                );
            }
        }
        return $result;
    }
}
