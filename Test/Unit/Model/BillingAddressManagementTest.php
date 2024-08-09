<?php

namespace Hryvinskyi\QuoteAddressValidator\Test\Unit\Model;

use Hryvinskyi\QuoteAddressValidator\Model\BillingAddressManagement;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\QuoteAddressValidator;
use Magento\Quote\Model\ShippingAddressAssignment;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BillingAddressManagementTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var BillingAddressManagement
     */
    protected $model;

    /**
     * @var MockObject
     */
    protected $quoteRepositoryMock;

    /**
     * @var MockObject
     */
    protected $validatorMock;

    /**
     * @var MockObject
     */
    protected $addressRepository;

    /**
     * @var MockObject
     */
    protected $shippingAssignmentMock;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->quoteRepositoryMock = $this->getMockForAbstractClass(CartRepositoryInterface::class);
        $this->validatorMock = $this->createMock(QuoteAddressValidator::class);
        $this->addressRepository = $this->getMockForAbstractClass(AddressRepositoryInterface::class);
        $logger = $this->getMockForAbstractClass(LoggerInterface::class);
        $this->model = $this->objectManager->getObject(
            BillingAddressManagement::class,
            [
                'quoteRepository' => $this->quoteRepositoryMock,
                'addressValidator' => $this->validatorMock,
                'logger' => $logger,
                'addressRepository' => $this->addressRepository
            ]
        );

        $this->shippingAssignmentMock = $this->createPartialMock(
            ShippingAddressAssignment::class,
            ['setAddress']
        );
        $this->objectManager->setBackwardCompatibleProperty(
            $this->model,
            'shippingAddressAssignment',
            $this->shippingAssignmentMock
        );
    }

    /**
     * @return void
     */
    public function testGetAddress()
    {
        $quoteMock = $this->createMock(Quote::class);
        $this->quoteRepositoryMock->expects($this->once())->method('getActive')
            ->with('cartId')->willReturn($quoteMock);

        $addressMock = $this->createMock(Address::class);
        $quoteMock->expects($this->any())->method('getBillingAddress')->willReturn($addressMock);

        $this->assertEquals($addressMock, $this->model->get('cartId'));
    }

    /**
     * @return void
     */
    public function testSetAddress()
    {
        $cartId = 100;
        $useForShipping = true;
        $addressId = 1;

        $address = $this->createPartialMock(Address::class, ['getId']);
        $quoteMock = $this->createPartialMock(
            Quote::class,
            ['removeAddress', 'getBillingAddress', 'setBillingAddress', 'setDataChanges']
        );

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($cartId)
            ->willReturn($quoteMock);

        $address->expects($this->exactly(2))->method('getId')->willReturn($addressId);
        $quoteMock->expects($this->exactly(2))->method('getBillingAddress')->willReturn($address);
        $quoteMock->expects($this->once())->method('removeAddress')->with($addressId)->willReturnSelf();
        $quoteMock->expects($this->once())->method('setBillingAddress')->with($address)->willReturnSelf();
        $quoteMock->expects($this->once())->method('setDataChanges')->with(1)->willReturnSelf();

        $this->shippingAssignmentMock->expects($this->once())
            ->method('setAddress')
            ->with($quoteMock, $address, $useForShipping);

        $this->quoteRepositoryMock->expects($this->once())->method('save')->with($quoteMock);
        $this->assertEquals($addressId, $this->model->assign($cartId, $address, $useForShipping));
    }

    /**
     * @return void
     */
    public function testSetAddressWithInabilityToSaveQuote()
    {
        $this->expectException('Magento\Framework\Exception\InputException');
        $this->expectExceptionMessage('The address failed to save. Verify the address and try again.');
        $cartId = 100;
        $addressId = 1;

        $address = $this->createPartialMock(Address::class, ['getId']);
        $quoteMock = $this->createPartialMock(
            Quote::class,
            ['removeAddress', 'getBillingAddress', 'setBillingAddress', 'setDataChanges']
        );

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($cartId)
            ->willReturn($quoteMock);

        $address->expects($this->once())->method('getId')->willReturn($addressId);
        $quoteMock->expects($this->once())->method('getBillingAddress')->willReturn($address);
        $quoteMock->expects($this->once())->method('removeAddress')->with($addressId)->willReturnSelf();
        $quoteMock->expects($this->once())->method('setBillingAddress')->with($address)->willReturnSelf();
        $quoteMock->expects($this->once())->method('setDataChanges')->with(1)->willReturnSelf();

        $this->shippingAssignmentMock->expects($this->once())
            ->method('setAddress')
            ->with($quoteMock, $address, false);

        $this->quoteRepositoryMock->expects($this->once())
            ->method('save')
            ->with($quoteMock)
            ->willThrowException(
                new \Exception('Some DB Error')
            );
        $this->model->assign($cartId, $address);
    }

    /**
     * @return void
     */
    public function testSetAddressWithInabilityByValidationToSaveQuote()
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('The billing information was unable to be saved. Error: "Some validation error"');
        $cartId = 100;
        $addressId = 1;

        $address = $this->createPartialMock(Address::class, ['getId']);
        $quoteMock = $this->createPartialMock(
            Quote::class,
            ['removeAddress', 'getBillingAddress', 'setBillingAddress', 'setDataChanges']
        );

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($cartId)
            ->willReturn($quoteMock);

        $address->expects($this->once())->method('getId')->willReturn($addressId);
        $quoteMock->expects($this->once())->method('getBillingAddress')->willReturn($address);
        $quoteMock->expects($this->once())->method('removeAddress')->with($addressId)->willReturnSelf();
        $quoteMock->expects($this->once())->method('setBillingAddress')->with($address)->willReturnSelf();

        $this->shippingAssignmentMock->expects($this->once())
            ->method('setAddress')
            ->with($quoteMock, $address, false)
            ->willThrowException(new LocalizedException(__('Some validation error')));

        $this->model->assign($cartId, $address);
    }
}
