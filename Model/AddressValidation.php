<?php

namespace Hryvinskyi\QuoteAddressValidator\Model;

use Magento\Quote\Api\Data\AddressInterface;

class AddressValidation implements AddressValidationInterface
{
    /**
     * @var ValidationInterface[]
     */
    private array $validations = [];

    /**
     * @param ValidationInterface[] $validations
     */
    public function __construct(array $validations = [])
    {
        // check if all validations are instances of ValidationInterface
        foreach ($validations as $validation) {
            if (!$validation instanceof ValidationInterface) {
                throw new \InvalidArgumentException('All validations must be instances of ValidationInterface');
            }
        }

        $this->validations = $validations;
    }

    /**
     * @inheritDoc
     */
    public function validate(AddressInterface $address): void
    {
        foreach ($this->validations as $validation) {
            $validation->execute($address);
        }
    }
}
