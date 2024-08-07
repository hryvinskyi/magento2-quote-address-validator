<?php

namespace Hryvinskyi\QuoteAddressValidator\Model;

use Magento\Quote\Api\Data\AddressInterface;

interface AddressValidationInterface
{
    /**
     * Validate the address.
     *
     * @param AddressInterface $address Address to validate
     * @throw \Magento\Framework\Exception\LocalizedException If the address is invalid
     */
    public function validate(AddressInterface $address): void;
}
