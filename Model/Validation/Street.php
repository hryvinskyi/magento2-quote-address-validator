<?php

namespace Hryvinskyi\QuoteAddressValidator\Model\Validation;

use Hryvinskyi\QuoteAddressValidator\Model\ConfigInterface;
use Hryvinskyi\QuoteAddressValidator\Model\ValidationInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\AddressInterface;

class Street extends AbstractValidator
{
    /**
     * @inheritDoc
     */
    public function execute(AddressInterface $address): bool
    {
        if (!$this->getConfig()->isEnabledStreet() || empty($address->getStreet()) || !is_array($address->getStreet())) {
            return true;
        }

        $pattern = $this->getConfig()->getStreetRegex();

        foreach ($address->getStreet() as $value) {
            $value = (string)$value;
            if ($value !== '' && $this->validate($value, $pattern, $this->getConfig()->getStreetStopwords()) === false) {
                throw new LocalizedException(__($this->getConfig()->getStreetErrorMessage(), $value));
            }
        }

        return true;
    }
}
