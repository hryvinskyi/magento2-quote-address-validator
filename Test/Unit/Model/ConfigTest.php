<?php

namespace Hryvinskyi\QuoteAddressValidator\Test\Unit\Model;

use Hryvinskyi\QuoteAddressValidator\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @var Config
     */
    protected $model;

    /**
     * @var ScopeConfigInterface|MockObject
     */
    protected $scopeConfigMock;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->scopeConfigMock = $this->getMockBuilder(ScopeConfigInterface::class)
            ->setMethods(['getValue', 'isSetFlag'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $objectManager = new ObjectManagerHelper($this);
        $this->model = $objectManager->getObject(
            Config::class,
            [
                'scopeConfig' => $this->scopeConfigMock
            ]
        );
    }

    /**
     * Test isEnabled()
     *
     * @return void
     * @dataProvider isEnabledDataProvider
     */
    public function testisEnabled($isSetFlag, $result): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(Config::XML_CONF_ENABLED, ScopeInterface::SCOPE_STORE)
            ->willReturn($isSetFlag);

        $this->assertEquals($result, $this->model->isEnabled());
    }

    /**
     * Data provider for isEnabled()
     *
     * @return array
     */
    public function isEnabledDataProvider(): array
    {
        return [
            [true, true],
            [false, false],
            [true, 1],
            [false, 0],
            [false, null],
            [false, '']
        ];
    }

    /**
     * Test getValidationType()
     *
     * @return void
     */
    public function testGetValidationType(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_CONF_VALIDATION_TYPE, ScopeInterface::SCOPE_STORE)
            ->willReturn('1');

        $this->assertEquals(1, $this->model->getValidationType());
    }

    /**
     * Test isEnabledFirstname()
     *
     * @return void
     */
    public function testIsEnabledFirstname(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(Config::XML_CONF_ENABLED_FIRSTNAME, ScopeInterface::SCOPE_STORE)
            ->willReturn(1);

        $this->assertEquals(true, $this->model->isEnabledFirstname());
    }

    /**
     * Test isEnabledLastname()
     *
     * @return void
     */
    public function testIsEnabledLastname(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(Config::XML_CONF_ENABLED_LASTNAME, ScopeInterface::SCOPE_STORE)
            ->willReturn(1);

        $this->assertEquals(true, $this->model->isEnabledLastname());
    }

    /**
     * Test isEnabledStreet()
     *
     * @return void
     */
    public function testIsEnabledStreet(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(Config::XML_CONF_ENABLED_STREET, ScopeInterface::SCOPE_STORE)
            ->willReturn(1);

        $this->assertEquals(true, $this->model->isEnabledStreet());
    }

    /**
     * Test getFirstnameStopwords()
     *
     * @return void
     */
    public function testGetFirstnameStopwords(): void
    {
        $stopwords = 'test,test,example,Example';
        $expected = ['test', 'example', 'Example'];
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_CONF_FIRSTNAME_STOPWORDS, ScopeInterface::SCOPE_STORE)
            ->willReturn($stopwords);

        $this->assertEquals($expected, $this->model->getFirstnameStopwords());
    }

    /**
     * Test getLastnameStopwords()
     *
     * @return void
     */
    public function testGetLastnameStopwords(): void
    {
        $stopwords = 'test,test, example, Example';
        $expected = ['test', 'example', 'Example'];
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_CONF_LASTNAME_STOPWORDS, ScopeInterface::SCOPE_STORE)
            ->willReturn($stopwords);

        $this->assertEquals($expected, $this->model->getLastnameStopwords());
    }

    /**
     * Test getStreetStopwords()
     *
     * @return void
     */
    public function testGetStreetStopwords(): void
    {
        $stopwords = 'test,test, example, Example';
        $expected = ['test', 'example', 'Example'];
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_CONF_STREET_STOPWORDS, ScopeInterface::SCOPE_STORE)
            ->willReturn($stopwords);

        $this->assertEquals($expected, $this->model->getStreetStopwords());
    }

    /**
     * Test getFirstnameRegex()
     *
     * @return void
     */
    public function testGetFirstnameRegex(): void
    {
        $regex = '/^[a-zA-Z]+$/';
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_CONF_FIRSTNAME_REGEX, ScopeInterface::SCOPE_STORE)
            ->willReturn($regex);

        $this->assertEquals($regex, $this->model->getFirstnameRegex());
    }

    /**
     * Test getLastnameRegex()
     *
     * @return void
     */
    public function testGetLastnameRegex(): void
    {
        $regex = '/^[a-zA-Z]+$/';
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_CONF_LASTNAME_REGEX, ScopeInterface::SCOPE_STORE)
            ->willReturn($regex);

        $this->assertEquals($regex, $this->model->getLastnameRegex());
    }

    /**
     * Test getStreetRegex()
     *
     * @return void
     */
    public function testGetStreetRegex(): void
    {
        $regex = '/^[a-zA-Z0-9\s]+$/';
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_CONF_STREET_REGEX, ScopeInterface::SCOPE_STORE)
            ->willReturn($regex);

        $this->assertEquals($regex, $this->model->getStreetRegex());
    }

    /**
     * Test getFirstnameErrorMessage()
     *
     * @return void
     */
    public function testGetFirstnameErrorMessage(): void
    {
        $errorMessage = 'Invalid firstname';
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_CONF_FIRSTNAME_ERROR_MESSAGE, ScopeInterface::SCOPE_STORE)
            ->willReturn($errorMessage);

        $this->assertEquals($errorMessage, $this->model->getFirstnameErrorMessage());
    }

    /**
     * Test getLastnameErrorMessage()
     *
     * @return void
     */
    public function testGetLastnameErrorMessage(): void
    {
        $errorMessage = 'Invalid lastname';
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_CONF_LASTNAME_ERROR_MESSAGE, ScopeInterface::SCOPE_STORE)
            ->willReturn($errorMessage);

        $this->assertEquals($errorMessage, $this->model->getLastnameErrorMessage());
    }

    /**
     * Test getStreetErrorMessage()
     *
     * @return void
     */
    public function testGetStreetErrorMessage(): void
    {
        $errorMessage = 'Invalid street';
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_CONF_STREET_ERROR_MESSAGE, ScopeInterface::SCOPE_STORE)
            ->willReturn($errorMessage);

        $this->assertEquals($errorMessage, $this->model->getStreetErrorMessage());
    }
}
