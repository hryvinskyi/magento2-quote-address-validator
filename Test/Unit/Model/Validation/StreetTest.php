<?php

namespace Hryvinskyi\QuoteAddressValidator\Test\Unit\Model\Validation;

use Hryvinskyi\QuoteAddressValidator\Model\ConfigInterface;
use Hryvinskyi\QuoteAddressValidator\Model\Validation\Street;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\AddressInterface;
use PHPUnit\Framework\TestCase;

class StreetTest extends TestCase
{
    private $configMock;
    private $addressMock;
    private $streetValidator;

    public const STREET_REGEX = '/^([\p{L}0-9&#$€£¥¢%&?!()@_:;,\'+\s\-\.\*\/\\\\]*)$/u';
    public const STREET_STOPW = '{{,}},gettemplate,base64_,afterfiltercall,.filter(';
    public const STREET_ERROR = 'Street "%1" is not valid, please use only letters, spaces, hyphens (-), apostrophes (\'). Street must be between 2 and 50 characters.';

    protected function setUp(): void
    {
        $this->configMock = $this->createMock(ConfigInterface::class);
        $this->addressMock = $this->createMock(AddressInterface::class);
        $this->streetValidator = new Street($this->configMock);
        $this->configMock->method('getStreetRegex')->willReturn(static::STREET_REGEX);
        $this->configMock->method('getStreetStopwords')->willReturn(explode(',', static::STREET_STOPW));
        $this->configMock->method('getStreetErrorMessage')->willReturn(static::STREET_ERROR);
        preg_match(static::STREET_REGEX, '');
    }

    public function testExecuteValidationDisabled()
    {
        // Test case when the last name validation is disabled
        $this->configMock->method('isEnabledStreet')->willReturn(false);
        $this->addressMock->method('getStreet')->willReturn('{{template}} &#$€£¥¢%&?!()@_:;,.*+');

        $result = $this->streetValidator->execute($this->addressMock);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider validStreetProvider
     */
    public function testExecuteValidStreetRegexOnly($street)
    {
        $this->configMock->method('getValidationType')->willReturn(1);
        $this->configMock->method('isEnabledStreet')->willReturn(true);
        $this->addressMock->method('getStreet')->willReturn($street);

        $result = $this->streetValidator->execute($this->addressMock);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider validStreetProvider
     */
    public function testExecuteValidStreetStopWordsOnly($street)
    {
        $this->configMock->method('getValidationType')->willReturn(2);
        $this->configMock->method('isEnabledStreet')->willReturn(true);
        $this->addressMock->method('getStreet')->willReturn($street);

        $result = $this->streetValidator->execute($this->addressMock);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider validStreetProvider
     */
    public function testExecuteValidStreetRegexAndStopWords($street)
    {
        $this->configMock->method('getValidationType')->willReturn(3);
        $this->configMock->method('isEnabledStreet')->willReturn(true);
        $this->addressMock->method('getStreet')->willReturn($street);

        $result = $this->streetValidator->execute($this->addressMock);
        $this->assertTrue($result);
    }

    public function validStreetProvider()
    {
        return [
            [null],
            [''],
            [
                ['Pine Street Apt 7B'],
                ['Pine Street Apt 7B'],
            ],
            [
                ['Pine Street Apt 7B +'],
                ['Pine Street Apt 7B +'],
            ],
            [
                ['Grünerstraße 42'],
                ['Grünerstraße 42'],
            ],
            [
                ['Reithmeier-Wolfgang Ave'],
                ['Reithmeier-Wolfgang Ave'],
            ],
            [
                ['#1 Victory Plaza'],
                ['#1 Victory Plaza'],
            ],
            [
                ['B2B Complex'],
                ['B2B Complex'],
            ],
            [
                ['Order #1234'],
                ['Order #1234'],
            ],
            [
                ['Pine Street Apt 7B'],
                ['Pine Street Apt 7B'],
            ],
            [
                ['Unit 7/8 Queens Avenue , '],
                ['Unit 7/8 Queens Avenue , '],
            ],
            [
                ['Unit 7\8 Queens Avenue &'],
                ['Unit 7\8 Queens Avenue &'],
            ],
            [
                ['Unit 7\8 Queens Avenue #'],
                ['Unit 7\8 Queens Avenue #'],
            ],
            [
                ['Unit 7\8 Queens Avenue $'],
                ['Unit 7\8 Queens Avenue $'],
            ],
            [
                ['Unit 7\8 Queens Avenue €'],
                ['Unit 7\8 Queens Avenue €'],
            ],
            [
                ['Unit 7\8 Queens Avenue £¥¢%&?!()@_:;,'],
                ['Unit 7\8 Queens Avenue £¥¢%&?!()@_:;,'],
            ]
        ];
    }

    /**
     * @dataProvider invalidStreetProvider
     */
    public function testExecuteInvalidStreetRegexOnly($street, $expectedMessage)
    {
        $this->configMock->method('getValidationType')->willReturn(1);
        $this->configMock->method('isEnabledStreet')->willReturn(true);
        $this->addressMock->method('getStreet')->willReturn($street);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage($expectedMessage);

        $result = $this->streetValidator->execute($this->addressMock);
    }
    /**
     * @dataProvider invalidStreetStopWordProvider
     */
    public function testExecuteInvalidStreetStopWordOnly($street, $expectedMessage)
    {
        $this->configMock->method('getValidationType')->willReturn(2);
        $this->configMock->method('isEnabledStreet')->willReturn(true);
        $this->addressMock->method('getStreet')->willReturn($street);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage($expectedMessage);

        $result = $this->streetValidator->execute($this->addressMock);
    }


    public function invalidStreetStopWordProvider()
    {
        return [
            [['gettemplate'], str_replace('%1', 'gettemplate', static::STREET_ERROR)],
            [['base64_'], str_replace('%1', 'base64_', static::STREET_ERROR)],
            [['afterfiltercall'], str_replace('%1', 'afterfiltercall', static::STREET_ERROR)],
            [['.filter('], str_replace('%1', '.filter(', static::STREET_ERROR)],
            [['{{'], str_replace('%1', '{{', static::STREET_ERROR)],
            [['}}'], str_replace('%1', '}}', static::STREET_ERROR)],
        ];
    }

    /**
     * @dataProvider invalidStreetStopWordRegexProvider
     */
    public function testExecuteInvalidStreetRegexAndStopWord($street, $expectedMessage)
    {
        $this->configMock->method('getValidationType')->willReturn(3);
        $this->configMock->method('isEnabledStreet')->willReturn(true);
        $this->addressMock->method('getStreet')->willReturn($street);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage($expectedMessage);

        $result = $this->streetValidator->execute($this->addressMock);
    }

    public function invalidStreetProvider()
    {
        return [
            [['{{template}}'], str_replace('%1', '{{template}}', static::STREET_ERROR)],
            [['{{var this.getTemplateFilter().filter(firstname)}}'], str_replace('%1', '{{var this.getTemplateFilter().filter(firstname)}}', static::STREET_ERROR)],
            [['{{var this.getTemp%00lateFilter().filter(firstname)}}'], str_replace('%1', '{{var this.getTemp%00lateFilter().filter(firstname)}}', static::STREET_ERROR)],
            [['{{var this.filter(firstname)}}'], str_replace('%1', '{{var this.filter(firstname)}}', static::STREET_ERROR)],
            [['Grünerstraße {{'], str_replace('%1', 'Grünerstraße {{', static::STREET_ERROR)],
            [['Grünerstraße }}'], str_replace('%1', 'Grünerstraße }}', static::STREET_ERROR)],
            [['Grünerstraße {'], str_replace('%1', 'Grünerstraße {', static::STREET_ERROR)],
            [['Grünerstraße }'], str_replace('%1', 'Grünerstraße }', static::STREET_ERROR)],
            [['Grünerstraße ['], str_replace('%1', 'Grünerstraße [', static::STREET_ERROR)],
            [['Grünerstraße ]'], str_replace('%1', 'Grünerstraße ]', static::STREET_ERROR)],
            [['Grünerstraße <'], str_replace('%1', 'Grünerstraße <', static::STREET_ERROR)],
            [['Grünerstraße >'], str_replace('%1', 'Grünerstraße >', static::STREET_ERROR)],
            [['Grünerstraße ='], str_replace('%1', 'Grünerstraße =', static::STREET_ERROR)],
            [['Grünerstraße ^'], str_replace('%1', 'Grünerstraße ^', static::STREET_ERROR)],
            [['Grünerstraße ~'], str_replace('%1', 'Grünerstraße ~', static::STREET_ERROR)],
            [['{{Grünerstraße'], str_replace('%1', '{{Grünerstraße', static::STREET_ERROR)],
            [['}}Grünerstraße'], str_replace('%1', '}}Grünerstraße', static::STREET_ERROR)],
            [['{Grünerstraße'], str_replace('%1', '{Grünerstraße', static::STREET_ERROR)],
            [['}Grünerstraße'], str_replace('%1', '}Grünerstraße', static::STREET_ERROR)],
            [['[Grünerstraße'], str_replace('%1', '[Grünerstraße', static::STREET_ERROR)],
            [[']Grünerstraße'], str_replace('%1', ']Grünerstraße', static::STREET_ERROR)],
            [['<Grünerstraße'], str_replace('%1', '<Grünerstraße', static::STREET_ERROR)],
            [['>Grünerstraße'], str_replace('%1', '>Grünerstraße', static::STREET_ERROR)],
            [['=Grünerstraße'], str_replace('%1', '=Grünerstraße', static::STREET_ERROR)],
            [['^Grünerstraße'], str_replace('%1', '^Grünerstraße', static::STREET_ERROR)],
            [['~Grünerstraße'], str_replace('%1', '~Grünerstraße', static::STREET_ERROR)],
        ];
    }


    public function invalidStreetStopWordRegexProvider()
    {
        return [
            [['gettemplate'], str_replace('%1', 'gettemplate', static::STREET_ERROR)],
            [['base64_'], str_replace('%1', 'base64_', static::STREET_ERROR)],
            [['afterfiltercall'], str_replace('%1', 'afterfiltercall', static::STREET_ERROR)],
            [['.filter('], str_replace('%1', '.filter(', static::STREET_ERROR)],
            [['{{'], str_replace('%1', '{{', static::STREET_ERROR)],
            [['}}'], str_replace('%1', '}}', static::STREET_ERROR)],
            [['{{template}}'], str_replace('%1', '{{template}}', static::STREET_ERROR)],
            [['{{var this.getTemplateFilter().filter(lastname)}}'], str_replace('%1', '{{var this.getTemplateFilter().filter(lastname)}}', static::STREET_ERROR)],
            [['<invalid>'], str_replace('%1', '<invalid>', static::STREET_ERROR)],
            [['[brackets]'], str_replace('%1', '[brackets]', static::STREET_ERROR)],
            [['{}'], str_replace('%1', '{}', static::STREET_ERROR)],
            [['=equals'], str_replace('%1', '=equals', static::STREET_ERROR)],
            [['^caret'], str_replace('%1', '^caret', static::STREET_ERROR)],
            [['~tilde'], str_replace('%1', '~tilde', static::STREET_ERROR)],
            [['invalid~name'], str_replace('%1', 'invalid~name', static::STREET_ERROR)],
            [['{{'], str_replace('%1', '{{', static::STREET_ERROR)],
            [['}}'], str_replace('%1', '}}', static::STREET_ERROR)],
            [['{'], str_replace('%1', '{', static::STREET_ERROR)],
            [['}'], str_replace('%1', '}', static::STREET_ERROR)],
            [['['], str_replace('%1', '[', static::STREET_ERROR)],
            [[']'], str_replace('%1', ']', static::STREET_ERROR)],
            [['<'], str_replace('%1', '<', static::STREET_ERROR)],
            [['>'], str_replace('%1', '>', static::STREET_ERROR)],
            [['='], str_replace('%1', '=', static::STREET_ERROR)],
            [['^'], str_replace('%1', '^', static::STREET_ERROR)],
            [['~'], str_replace('%1', '~', static::STREET_ERROR)],
        ];
    }
}
