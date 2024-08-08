<?php

namespace Hryvinskyi\QuoteAddressValidator\Plugin;

use Hryvinskyi\QuoteAddressValidator\Model\AddressValidationInterface;
use Hryvinskyi\QuoteAddressValidator\Model\ConfigInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\QuoteAddressValidator;

class AddAttributesValidation
{
    /**
     * @var AddressValidationInterface 
     */
    private $addressValidation;

    /**
     * @var ConfigInterface 
     */
    private $config;

    public function __construct(
        AddressValidationInterface $addressValidation,
        ConfigInterface $config
    ) {
        $this->addressValidation = $addressValidation;
        $this->config = $config;
    }

    /**
     * @param QuoteAddressValidator $subject
     * @param null $result
     * @param CartInterface $cart
     * @param AddressInterface $address
     * @return void
     * @noinspection UnusedFormalParameterInspection
     * @noinspection PhpUnusedParameterInspection
     */
    public function afterValidateForCart(
        QuoteAddressValidator $subject,
        $result,
        CartInterface $cart,
        AddressInterface $address
    ): void {
        if ($this->config->isEnabled()) {
            $this->addressValidation->validate($address);
        }
    }

    /**
     * @param QuoteAddressValidator $subject
     * @param bool $result
     * @param AddressInterface $address
     * @return void
     */
    public function afterValidate(
        QuoteAddressValidator $subject,
        bool $result,
        AddressInterface $address
    ): bool {
        if ($this->config->isEnabled()) {
            $this->addressValidation->validate($address);
        }

        return $result;
    }
}
