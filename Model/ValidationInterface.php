<?php

namespace Hryvinskyi\QuoteAddressValidator\Model;

use Magento\Quote\Api\Data\AddressInterface;

interface ValidationInterface
{
    /**
     * Execute the validation.
     *
     * @param AddressInterface $address Address to validate
     * @return bool True if the value is valid, throws otherwise
     * @throws \Magento\Framework\Exception\LocalizedException If the value is not a string
     */
    public function execute(AddressInterface $address): bool;
}
