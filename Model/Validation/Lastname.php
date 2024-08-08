<?php

namespace Hryvinskyi\QuoteAddressValidator\Model\Validation;

use Hryvinskyi\QuoteAddressValidator\Model\ConfigInterface;
use Hryvinskyi\QuoteAddressValidator\Model\ValidationInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\AddressInterface;

class Lastname extends AbstractValidator
{
    /**
     * @inheritDoc
     */
    public function execute(AddressInterface $address): bool
    {
        if (!$this->getConfig()->isEnabledLastname()) {
            return true;
        }

        $value = (string)$address->getLastname();

        if ($value === '' || $this->validate($value, $this->getConfig()->getLastnameRegex(), $this->getConfig()->getLastnameStopwords())) {
            return true;
        }

        throw new LocalizedException(__($this->getConfig()->getLastnameErrorMessage(), $address->getLastname()));
    }
}
