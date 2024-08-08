<?php

namespace Hryvinskyi\QuoteAddressValidator\Test\Unit\Model\Validation;

use Hryvinskyi\QuoteAddressValidator\Model\ConfigInterface;
use Hryvinskyi\QuoteAddressValidator\Model\Validation\Lastname;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\AddressInterface;
use PHPUnit\Framework\TestCase;

class LastnameTest extends TestCase
{
    private $configMock;
    private $addressMock;
    private $lastnameValidator;

    public const LASTNAME_REGEX = '/^([\p{L}0-9&#$€£¥¢%&?!()@_:;,.*+\s\-\']{1,50})$/u';
    public const LASTNAME_STOPW = '{{,}},gettemplate,base64_,afterfiltercall,.filter(';
    public const LASTNAME_ERROR = 'Last Name "%1" is not valid, please use only letters, spaces, hyphens (-), apostrophes (\'). Last Name must be between 2 and 50 characters.';

    protected function setUp(): void
    {
        $this->configMock = $this->createMock(ConfigInterface::class);
        $this->addressMock = $this->createMock(AddressInterface::class);
        $this->lastnameValidator = new Lastname($this->configMock);
        $this->configMock->method('getLastnameRegex')->willReturn(static::LASTNAME_REGEX);
        $this->configMock->method('getLastnameStopwords')->willReturn(explode(',', static::LASTNAME_STOPW));
        $this->configMock->method('getLastnameErrorMessage')->willReturn(static::LASTNAME_ERROR);
    }

    public function testExecuteValidationDisabled()
    {
        $this->configMock->method('isEnabledLastname')->willReturn(false);
        $this->addressMock->method('getLastname')->willReturn('{{template}} &#$€£¥¢%&?!()@_:;,.*+');

        $result = $this->lastnameValidator->execute($this->addressMock);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider validLastnameProvider
     */
    public function testExecuteValidLastnameRegexOnly($lastname)
    {
        $this->configMock->method('getValidationType')->willReturn(1);
        $this->configMock->method('isEnabledLastname')->willReturn(true);
        $this->addressMock->method('getLastname')->willReturn($lastname);

        $result = $this->lastnameValidator->execute($this->addressMock);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider validLastnameProvider
     */
    public function testExecuteValidLastnameStopWordsOnly($lastname)
    {
        $this->configMock->method('getValidationType')->willReturn(2);
        $this->configMock->method('isEnabledLastname')->willReturn(true);
        $this->addressMock->method('getLastname')->willReturn($lastname);

        $result = $this->lastnameValidator->execute($this->addressMock);
        $this->assertTrue($result);
    }

    public function validLastnameProvider()
    {
        return [
            [null],
            [''],
            ['Reithmeier, Wolfgang'],
            ['Reithmeier Wolfgang'],
            ['Reithmeier-Wolfgang'],
            ['Reithmeier\'Wolfgang'],
            ['&#$€£¥¢%&?!()@_:;,.*+'],
            ['&'],
            ['#'],
            ['$'],
            ['€'],
            ['£'],
            ['¥'],
            ['¢'],
            ['%'],
            ['&'],
            ['?'],
            ['!'],
            ['('],
            [')'],
            ['@'],
            ['_'],
            [':'],
            [';'],
            [','],
            ['.'],
            ['*'],
            ['+'],
            ['B2B'],
            ['José'],
            ["O'Connor"],
            ['. Gerigk & Smiejczak GbR'],
            ['Order #1234'],
            ['Order #1234+']
        ];
    }

    /**
     * @dataProvider invalidLastnameProvider
     */
    public function testExecuteInvalidLastnameRegexOnly($lastname, $expectedMessage)
    {
        $this->configMock->method('getValidationType')->willReturn(1);
        $this->configMock->method('isEnabledLastname')->willReturn(true);
        $this->addressMock->method('getLastname')->willReturn($lastname);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage($expectedMessage);

        $result = $this->lastnameValidator->execute($this->addressMock);
    }

    public function invalidLastnameProvider()
    {
        return [
            ['{{template}}', str_replace('%1', '{{template}}', static::LASTNAME_ERROR)],
            ['{{var this.getTemplateFilter().filter(lastname)}}', str_replace('%1', '{{var this.getTemplateFilter().filter(lastname)}}', static::LASTNAME_ERROR)],
            ['<invalid>', str_replace('%1', '<invalid>', static::LASTNAME_ERROR)],
            ['[brackets]', str_replace('%1', '[brackets]', static::LASTNAME_ERROR)],
            ['{}', str_replace('%1', '{}', static::LASTNAME_ERROR)],
            ['=equals', str_replace('%1', '=equals', static::LASTNAME_ERROR)],
            ['^caret', str_replace('%1', '^caret', static::LASTNAME_ERROR)],
            ['~tilde', str_replace('%1', '~tilde', static::LASTNAME_ERROR)],
            ['invalid~name', str_replace('%1', 'invalid~name', static::LASTNAME_ERROR)],
            ['{{', str_replace('%1', '{{', static::LASTNAME_ERROR)],
            ['}}', str_replace('%1', '}}', static::LASTNAME_ERROR)],
            ['{', str_replace('%1', '{', static::LASTNAME_ERROR)],
            ['}', str_replace('%1', '}', static::LASTNAME_ERROR)],
            ['[', str_replace('%1', '[', static::LASTNAME_ERROR)],
            [']', str_replace('%1', ']', static::LASTNAME_ERROR)],
            ['<', str_replace('%1', '<', static::LASTNAME_ERROR)],
            ['>', str_replace('%1', '>', static::LASTNAME_ERROR)],
            ['=', str_replace('%1', '=', static::LASTNAME_ERROR)],
            ['^', str_replace('%1', '^', static::LASTNAME_ERROR)],
            ['~', str_replace('%1', '~', static::LASTNAME_ERROR)],
            [
                'Long text more tan 50 characters. Long text more tan 50 characters. Long text more tan 50 characters.',
                str_replace('%1', 'Long text more tan 50 characters. Long text more tan 50 characters. Long text more tan 50 characters.', static::LASTNAME_ERROR)
            ],
        ];
    }

    /**
     * @dataProvider invalidLastnameStopWordProvider
     */
    public function testLastnameInvalidFirstnameStopWordOnly($lastname, $expectedMessage)
    {
        $this->configMock->method('getValidationType')->willReturn(2);
        $this->configMock->method('isEnabledLastname')->willReturn(true);
        $this->addressMock->method('getLastname')->willReturn($lastname);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage($expectedMessage);

        $this->lastnameValidator->execute($this->addressMock);
    }

    public function invalidLastnameStopWordProvider()
    {
        return [
            ['gettemplate', str_replace('%1', 'gettemplate', static::LASTNAME_ERROR)],
            ['base64_', str_replace('%1', 'base64_', static::LASTNAME_ERROR)],
            ['afterfiltercall', str_replace('%1', 'afterfiltercall', static::LASTNAME_ERROR)],
            ['.filter(', str_replace('%1', '.filter(', static::LASTNAME_ERROR)],
            ['{{', str_replace('%1', '{{', static::LASTNAME_ERROR)],
            ['}}', str_replace('%1', '}}', static::LASTNAME_ERROR)],
        ];
    }

    /**
     * @dataProvider invalidFirstnameStopWordRegexProvider
     */
    public function testExecuteInvalidFirstnameRegexAndStopWord($lastname, $expectedMessage)
    {
        $this->configMock->method('getValidationType')->willReturn(3);
        $this->configMock->method('isEnabledLastname')->willReturn(true);
        $this->addressMock->method('getLastname')->willReturn($lastname);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage($expectedMessage);

        $this->lastnameValidator->execute($this->addressMock);
    }

    public function invalidFirstnameStopWordRegexProvider()
    {
        return [
            ['gettemplate', str_replace('%1', 'gettemplate', static::LASTNAME_ERROR)],
            ['base64_', str_replace('%1', 'base64_', static::LASTNAME_ERROR)],
            ['afterfiltercall', str_replace('%1', 'afterfiltercall', static::LASTNAME_ERROR)],
            ['.filter(', str_replace('%1', '.filter(', static::LASTNAME_ERROR)],
            ['{{', str_replace('%1', '{{', static::LASTNAME_ERROR)],
            ['}}', str_replace('%1', '}}', static::LASTNAME_ERROR)],
            ['{{template}}', str_replace('%1', '{{template}}', static::LASTNAME_ERROR)],
            ['{{var this.getTemplateFilter().filter(lastname)}}', str_replace('%1', '{{var this.getTemplateFilter().filter(lastname)}}', static::LASTNAME_ERROR)],
            ['<invalid>', str_replace('%1', '<invalid>', static::LASTNAME_ERROR)],
            ['[brackets]', str_replace('%1', '[brackets]', static::LASTNAME_ERROR)],
            ['{}', str_replace('%1', '{}', static::LASTNAME_ERROR)],
            ['=equals', str_replace('%1', '=equals', static::LASTNAME_ERROR)],
            ['^caret', str_replace('%1', '^caret', static::LASTNAME_ERROR)],
            ['~tilde', str_replace('%1', '~tilde', static::LASTNAME_ERROR)],
            ['invalid~name', str_replace('%1', 'invalid~name', static::LASTNAME_ERROR)],
            ['{{', str_replace('%1', '{{', static::LASTNAME_ERROR)],
            ['}}', str_replace('%1', '}}', static::LASTNAME_ERROR)],
            ['{', str_replace('%1', '{', static::LASTNAME_ERROR)],
            ['}', str_replace('%1', '}', static::LASTNAME_ERROR)],
            ['[', str_replace('%1', '[', static::LASTNAME_ERROR)],
            [']', str_replace('%1', ']', static::LASTNAME_ERROR)],
            ['<', str_replace('%1', '<', static::LASTNAME_ERROR)],
            ['>', str_replace('%1', '>', static::LASTNAME_ERROR)],
            ['=', str_replace('%1', '=', static::LASTNAME_ERROR)],
            ['^', str_replace('%1', '^', static::LASTNAME_ERROR)],
            ['~', str_replace('%1', '~', static::LASTNAME_ERROR)],
            [
                'Long text more tan 50 characters. Long text more tan 50 characters. Long text more tan 50 characters.',
                str_replace('%1', 'Long text more tan 50 characters. Long text more tan 50 characters. Long text more tan 50 characters.', static::LASTNAME_ERROR)
            ],
        ];
    }
}
