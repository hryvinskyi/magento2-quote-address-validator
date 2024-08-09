<?php

namespace Hryvinskyi\QuoteAddressValidator\Test\Unit\Plugin;

use PHPUnit\Framework\TestCase;
use Hryvinskyi\QuoteAddressValidator\Plugin\AddAttributesValidation;
use Hryvinskyi\QuoteAddressValidator\Model\AddressValidationInterface;
use Hryvinskyi\QuoteAddressValidator\Model\ConfigInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\QuoteAddressValidator;

class AddAttributesValidationTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|AddressValidationInterface
     */
    private $addressValidationMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ConfigInterface
     */
    private $configMock;

    /**
     * @var AddAttributesValidation
     */
    private $plugin;

    protected function setUp(): void
    {
        $this->addressValidationMock = $this->createMock(AddressValidationInterface::class);
        $this->configMock = $this->createMock(ConfigInterface::class);

        $this->plugin = new AddAttributesValidation(
            $this->addressValidationMock,
            $this->configMock
        );
    }

    public function testAfterValidateForCart()
    {
        $quoteAddressValidatorMock = $this->createMock(QuoteAddressValidator::class);
        $cartMock = $this->createMock(CartInterface::class);
        $addressMock = $this->createMock(AddressInterface::class);

        $this->configMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->addressValidationMock->expects($this->once())
            ->method('validate')
            ->with($addressMock);

        $this->plugin->afterValidateForCart($quoteAddressValidatorMock, null, $cartMock, $addressMock);
    }

    public function testAfterValidate()
    {
        $quoteAddressValidatorMock = $this->createMock(QuoteAddressValidator::class);
        $addressMock = $this->createMock(AddressInterface::class);

        $this->configMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->addressValidationMock->expects($this->once())
            ->method('validate')
            ->with($addressMock);

        $result = $this->plugin->afterValidate($quoteAddressValidatorMock, true, $addressMock);

        $this->assertTrue($result);
    }
}
