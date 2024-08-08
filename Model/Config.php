<?php

namespace Hryvinskyi\QuoteAddressValidator\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config implements ConfigInterface
{
    public const XML_CONF_ENABLED = 'quote_address_validator/general/enabled';
    public const XML_CONF_ENABLED_FIRSTNAME = 'quote_address_validator/general/enabled_firstname';
    public const XML_CONF_ENABLED_LASTNAME = 'quote_address_validator/general/enabled_lastname';
    public const XML_CONF_ENABLED_STREET = 'quote_address_validator/general/enabled_street';

    public const XML_CONF_VALIDATION_TYPE = 'quote_address_validator/general/validation_type';

    public const XML_CONF_FIRSTNAME_STOPWORDS = 'quote_address_validator/general/firstname_stopwords';
    public const XML_CONF_LASTNAME_STOPWORDS = 'quote_address_validator/general/lastname_stopwords';
    public const XML_CONF_STREET_STOPWORDS = 'quote_address_validator/general/street_stopwords';

    public const XML_CONF_FIRSTNAME_REGEX = 'quote_address_validator/general/firstname_regex';
    public const XML_CONF_LASTNAME_REGEX = 'quote_address_validator/general/lastname_regex';
    public const XML_CONF_STREET_REGEX = 'quote_address_validator/general/street_regex';

    public const XML_CONF_FIRSTNAME_ERROR_MESSAGE = 'quote_address_validator/general/firstname_error_message';
    public const XML_CONF_LASTNAME_ERROR_MESSAGE = 'quote_address_validator/general/lastname_error_message';
    public const XML_CONF_STREET_ERROR_MESSAGE = 'quote_address_validator/general/street_error_message';

    private ScopeConfigInterface $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritDoc
     */
    public function isEnabled($store = null, string $scope = ScopeInterface::SCOPE_STORE): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_CONF_ENABLED, $scope, $store);
    }

    /**
     * @inheritDoc
     */
    public function getValidationType($store = null, string $scope = ScopeInterface::SCOPE_STORE): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_CONF_VALIDATION_TYPE, $scope, $store);
    }

    /**
     * @inheritDoc
     */
    public function isEnabledFirstname($store = null, string $scope = ScopeInterface::SCOPE_STORE): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_CONF_ENABLED_FIRSTNAME, $scope, $store);
    }

    /**
     * @inheritDoc
     */
    public function isEnabledLastname($store = null, string $scope = ScopeInterface::SCOPE_STORE): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_CONF_ENABLED_LASTNAME, $scope, $store);
    }

    /**
     * @inheritDoc
     */
    public function isEnabledStreet($store = null, string $scope = ScopeInterface::SCOPE_STORE): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_CONF_ENABLED_STREET, $scope, $store);
    }

    /**
     * @inheritDoc
     */
    public function getFirstnameStopwords($store = null, string $scope = ScopeInterface::SCOPE_STORE): array
    {
        return array_filter(
            array_unique(
                explode(',', (string)$this->scopeConfig->getValue(self::XML_CONF_FIRSTNAME_STOPWORDS, $scope, $store))
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getLastnameStopwords($store = null, string $scope = ScopeInterface::SCOPE_STORE): array
    {
        return array_filter(
            array_unique(
                explode(',', (string)$this->scopeConfig->getValue(self::XML_CONF_LASTNAME_STOPWORDS, $scope, $store))
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getStreetStopwords($store = null, string $scope = ScopeInterface::SCOPE_STORE): array
    {
        return array_filter(
            array_unique(
                explode(',', (string)$this->scopeConfig->getValue(self::XML_CONF_STREET_STOPWORDS, $scope, $store))
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getFirstnameRegex($store = null, string $scope = ScopeInterface::SCOPE_STORE): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_CONF_FIRSTNAME_REGEX, $scope, $store);
    }

    /**
     * @inheritDoc
     */
    public function getLastnameRegex($store = null, string $scope = ScopeInterface::SCOPE_STORE): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_CONF_LASTNAME_REGEX, $scope, $store);
    }

    /**
     * @inheritDoc
     */
    public function getStreetRegex($store = null, string $scope = ScopeInterface::SCOPE_STORE): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_CONF_STREET_REGEX, $scope, $store);
    }

    /**
     * @inheritDoc
     */
    public function getFirstnameErrorMessage($store = null, string $scope = ScopeInterface::SCOPE_STORE): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_CONF_FIRSTNAME_ERROR_MESSAGE, $scope, $store);
    }

    /**
     * @inheritDoc
     */
    public function getLastnameErrorMessage($store = null, string $scope = ScopeInterface::SCOPE_STORE): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_CONF_LASTNAME_ERROR_MESSAGE, $scope, $store);
    }

    /**
     * @inheritDoc
     */
    public function getStreetErrorMessage($store = null, string $scope = ScopeInterface::SCOPE_STORE): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_CONF_STREET_ERROR_MESSAGE, $scope, $store);
    }
}
