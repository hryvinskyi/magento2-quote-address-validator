<?php

namespace Hryvinskyi\QuoteAddressValidator\Model\Validation;

use Hryvinskyi\QuoteAddressValidator\Model\ConfigInterface;
use Hryvinskyi\QuoteAddressValidator\Model\ValidationInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\AddressInterface;

class Lastname implements ValidationInterface
{
    private ConfigInterface $config;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function execute(AddressInterface $address): bool
    {
        if (!$this->config->isEnabledLastname()) {
            return true;
        }

        $pattern = $this->config->getLastnameRegex();
        $value = (string)$address->getLastname();

        if ($value === '' || preg_match($pattern, $value) === 1) {
            return true;
        }

        throw new LocalizedException(__($this->config->getLastnameErrorMessage(), $address->getLastname()));
    }
}
