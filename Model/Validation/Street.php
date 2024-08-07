<?php

namespace Hryvinskyi\QuoteAddressValidator\Model\Validation;

use Hryvinskyi\QuoteAddressValidator\Model\ConfigInterface;
use Hryvinskyi\QuoteAddressValidator\Model\ValidationInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\AddressInterface;

class Street implements ValidationInterface
{
    private ConfigInterface $config;

    /**
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function execute(AddressInterface $address): bool
    {
        if (!$this->config->isEnabledStreet()) {
            return true;
        }

        $pattern = $this->config->getStreetRegex();

        foreach ($address->getStreet() as $value) {
            $value = (string)$value;
            if ($value !== '' && preg_match($pattern, $value) !== 1) {
                throw new LocalizedException(__($this->config->getStreetErrorMessage(), $value));
            }
        }

        return true;
    }
}
