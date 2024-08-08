<?php

namespace Hryvinskyi\QuoteAddressValidator\Test\Unit\Model\Validation;

use Hryvinskyi\QuoteAddressValidator\Model\ConfigInterface;
use Hryvinskyi\QuoteAddressValidator\Model\Validation\Firstname;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\AddressInterface;
use PHPUnit\Framework\TestCase;

class FirstnameTest extends TestCase
{
    private $configMock;
    private $addressMock;
    private $firstnameValidator;

    public const FIRSTNAME_REGEX = '/^([\p{L}0-9&#$€£¥¢%&?!()@_:;,.*+\s\-\']{1,50})$/u';
    public const FIRSTNAME_STOPW = '{{,}},gettemplate,base64_,afterfiltercall,.filter(';
    public const FIRSTNAME_ERROR = 'First Name "%1" is not valid, please use only letters, spaces, hyphens (-), apostrophes (\'). First Name must be between 2 and 50 characters.';

    protected function setUp(): void
    {
        $this->configMock = $this->createMock(ConfigInterface::class);
        $this->addressMock = $this->createMock(AddressInterface::class);
        $this->firstnameValidator = new Firstname($this->configMock);

        $this->configMock->method('getFirstnameRegex')->willReturn(static::FIRSTNAME_REGEX);
        $this->configMock->method('getFirstnameStopwords')->willReturn(explode(',', static::FIRSTNAME_STOPW));
        $this->configMock->method('getFirstnameErrorMessage')->willReturn(static::FIRSTNAME_ERROR);
    }

    public function testExecuteValidationDisabled()
    {
        $this->configMock->method('isEnabledFirstname')->willReturn(false);
        $this->addressMock->method('getLastname')->willReturn('{{template}} &#$€£¥¢%&?!()@_:;,.*+');
        $result = $this->firstnameValidator->execute($this->addressMock);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider validFirstnameProvider
     */
    public function testExecuteValidFirstnameRegexOnly($firstname)
    {
        $this->configMock->method('getValidationType')->willReturn(1);
        $this->configMock->method('isEnabledFirstname')->willReturn(true);
        $this->addressMock->method('getFirstname')->willReturn($firstname);

        $result = $this->firstnameValidator->execute($this->addressMock);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider validFirstnameProvider
     */
    public function testExecuteValidFirstnameStopWordsOnly($firstname)
    {
        $this->configMock->method('getValidationType')->willReturn(2);
        $this->configMock->method('isEnabledFirstname')->willReturn(true);
        $this->addressMock->method('getFirstname')->willReturn($firstname);

        $result = $this->firstnameValidator->execute($this->addressMock);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider validFirstnameProvider
     */
    public function testExecuteValidFirstnameRegexAndStopWords($firstname)
    {
        $this->configMock->method('getValidationType')->willReturn(3);
        $this->configMock->method('isEnabledFirstname')->willReturn(true);
        $this->addressMock->method('getFirstname')->willReturn($firstname);

        $result = $this->firstnameValidator->execute($this->addressMock);
        $this->assertTrue($result);
    }

    public function validFirstnameProvider()
    {
        return [
            [null],
            [''],
            ['Reithmeier'],
            ['Wolfgang'],
            ['Reithmeier-Wolfgang'],
            ['O\'Connor'],
            ['Gerigk & Smiejczak GbR'],
            ['12345'],
            ['José'],
            ['Nguyễn'],
            ['Zhang Wei'],
            ['El-Masry'],
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
     * @dataProvider invalidFirstnameProvider
     */
    public function testExecuteInvalidFirstnameRegexOnly($firstname, $expectedMessage)
    {
        $this->configMock->method('getValidationType')->willReturn(1);
        $this->configMock->method('isEnabledFirstname')->willReturn(true);
        $this->addressMock->method('getFirstname')->willReturn($firstname);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage($expectedMessage);

        $this->firstnameValidator->execute($this->addressMock);
    }

    /**
     * @dataProvider invalidFirstnameStopWordProvider
     */
    public function testExecuteInvalidFirstnameStopWordOnly($firstname, $expectedMessage)
    {
        $this->configMock->method('getValidationType')->willReturn(2);
        $this->configMock->method('isEnabledFirstname')->willReturn(true);
        $this->addressMock->method('getFirstname')->willReturn($firstname);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage($expectedMessage);

        $this->firstnameValidator->execute($this->addressMock);
    }

    /**
     * @dataProvider invalidFirstnameStopWordRegexProvider
     */
    public function testExecuteInvalidFirstnameRegexAndStopWord($firstname, $expectedMessage)
    {
        $this->configMock->method('getValidationType')->willReturn(3);
        $this->configMock->method('isEnabledFirstname')->willReturn(true);
        $this->addressMock->method('getFirstname')->willReturn($firstname);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage($expectedMessage);

        $this->firstnameValidator->execute($this->addressMock);
    }

    public function invalidFirstnameStopWordProvider()
    {
        return [
            ['gettemplate', str_replace('%1', 'gettemplate', static::FIRSTNAME_ERROR)],
            ['base64_', str_replace('%1', 'base64_', static::FIRSTNAME_ERROR)],
            ['afterfiltercall', str_replace('%1', 'afterfiltercall', static::FIRSTNAME_ERROR)],
            ['.filter(', str_replace('%1', '.filter(', static::FIRSTNAME_ERROR)],
            ['{{', str_replace('%1', '{{', static::FIRSTNAME_ERROR)],
            ['}}', str_replace('%1', '}}', static::FIRSTNAME_ERROR)],
        ];
    }

    public function invalidFirstnameProvider()
    {
        return [
            ['{{template}}', str_replace('%1', '{{template}}', static::FIRSTNAME_ERROR)],
            ['{{var this.getTemplateFilter().filter(lastname)}}', str_replace('%1', '{{var this.getTemplateFilter().filter(lastname)}}', static::FIRSTNAME_ERROR)],
            ['<invalid>', str_replace('%1', '<invalid>', static::FIRSTNAME_ERROR)],
            ['[brackets]', str_replace('%1', '[brackets]', static::FIRSTNAME_ERROR)],
            ['{}', str_replace('%1', '{}', static::FIRSTNAME_ERROR)],
            ['=equals', str_replace('%1', '=equals', static::FIRSTNAME_ERROR)],
            ['^caret', str_replace('%1', '^caret', static::FIRSTNAME_ERROR)],
            ['~tilde', str_replace('%1', '~tilde', static::FIRSTNAME_ERROR)],
            ['invalid~name', str_replace('%1', 'invalid~name', static::FIRSTNAME_ERROR)],
            ['{{', str_replace('%1', '{{', static::FIRSTNAME_ERROR)],
            ['}}', str_replace('%1', '}}', static::FIRSTNAME_ERROR)],
            ['{', str_replace('%1', '{', static::FIRSTNAME_ERROR)],
            ['}', str_replace('%1', '}', static::FIRSTNAME_ERROR)],
            ['[', str_replace('%1', '[', static::FIRSTNAME_ERROR)],
            [']', str_replace('%1', ']', static::FIRSTNAME_ERROR)],
            ['<', str_replace('%1', '<', static::FIRSTNAME_ERROR)],
            ['>', str_replace('%1', '>', static::FIRSTNAME_ERROR)],
            ['=', str_replace('%1', '=', static::FIRSTNAME_ERROR)],
            ['^', str_replace('%1', '^', static::FIRSTNAME_ERROR)],
            ['~', str_replace('%1', '~', static::FIRSTNAME_ERROR)],
            [
                'Long text more tan 50 characters. Long text more tan 50 characters. Long text more tan 50 characters.',
                str_replace('%1', 'Long text more tan 50 characters. Long text more tan 50 characters. Long text more tan 50 characters.', static::FIRSTNAME_ERROR)
            ],
        ];
    }

    public function invalidFirstnameStopWordRegexProvider()
    {
        return [
            ['gettemplate', str_replace('%1', 'gettemplate', static::FIRSTNAME_ERROR)],
            ['base64_', str_replace('%1', 'base64_', static::FIRSTNAME_ERROR)],
            ['afterfiltercall', str_replace('%1', 'afterfiltercall', static::FIRSTNAME_ERROR)],
            ['.filter(', str_replace('%1', '.filter(', static::FIRSTNAME_ERROR)],
            ['{{', str_replace('%1', '{{', static::FIRSTNAME_ERROR)],
            ['}}', str_replace('%1', '}}', static::FIRSTNAME_ERROR)],
            ['{{template}}', str_replace('%1', '{{template}}', static::FIRSTNAME_ERROR)],
            ['{{var this.getTemplateFilter().filter(lastname)}}', str_replace('%1', '{{var this.getTemplateFilter().filter(lastname)}}', static::FIRSTNAME_ERROR)],
            ['<invalid>', str_replace('%1', '<invalid>', static::FIRSTNAME_ERROR)],
            ['[brackets]', str_replace('%1', '[brackets]', static::FIRSTNAME_ERROR)],
            ['{}', str_replace('%1', '{}', static::FIRSTNAME_ERROR)],
            ['=equals', str_replace('%1', '=equals', static::FIRSTNAME_ERROR)],
            ['^caret', str_replace('%1', '^caret', static::FIRSTNAME_ERROR)],
            ['~tilde', str_replace('%1', '~tilde', static::FIRSTNAME_ERROR)],
            ['invalid~name', str_replace('%1', 'invalid~name', static::FIRSTNAME_ERROR)],
            ['{{', str_replace('%1', '{{', static::FIRSTNAME_ERROR)],
            ['}}', str_replace('%1', '}}', static::FIRSTNAME_ERROR)],
            ['{', str_replace('%1', '{', static::FIRSTNAME_ERROR)],
            ['}', str_replace('%1', '}', static::FIRSTNAME_ERROR)],
            ['[', str_replace('%1', '[', static::FIRSTNAME_ERROR)],
            [']', str_replace('%1', ']', static::FIRSTNAME_ERROR)],
            ['<', str_replace('%1', '<', static::FIRSTNAME_ERROR)],
            ['>', str_replace('%1', '>', static::FIRSTNAME_ERROR)],
            ['=', str_replace('%1', '=', static::FIRSTNAME_ERROR)],
            ['^', str_replace('%1', '^', static::FIRSTNAME_ERROR)],
            ['~', str_replace('%1', '~', static::FIRSTNAME_ERROR)],
            [
                'Long text more tan 50 characters. Long text more tan 50 characters. Long text more tan 50 characters.',
                str_replace('%1', 'Long text more tan 50 characters. Long text more tan 50 characters. Long text more tan 50 characters.', static::FIRSTNAME_ERROR)
            ],
        ];
    }
}
