<?php

namespace Hryvinskyi\QuoteAddressValidator\Model\Validation;

use Hryvinskyi\QuoteAddressValidator\Model\ConfigInterface;
use Hryvinskyi\QuoteAddressValidator\Model\ValidationInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\AddressInterface;

class Firstname extends AbstractValidator
{

    /**
     * @inheritDoc
     */
    public function execute(AddressInterface $address): bool
    {
        if (!$this->getConfig()->isEnabledFirstname()) {
            return true;
        }

        $value = (string)$address->getFirstname();

        if ($value === '' || $this->validate($value, $this->getConfig()->getFirstnameRegex(), $this->getConfig()->getFirstnameStopwords())) {
            return true;
        }

        throw new LocalizedException(__($this->getConfig()->getFirstnameErrorMessage(), $address->getFirstname()));
    }
}
