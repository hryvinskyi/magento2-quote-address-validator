<?php

namespace Hryvinskyi\QuoteAddressValidator\Model;

use Magento\Store\Model\ScopeInterface;

interface ConfigInterface
{
    /**
     * Check if the address validation is enabled.
     *
     * @param null|int|string|\Magento\Store\Api\Data\StoreInterface $store Store ID or store code or store object (optional)
     * @param string $scope Scope of the configuration value (optional)
     * @return bool True if enabled, false otherwise
     */
    public function isEnabled($store = null, string $scope = ScopeInterface::SCOPE_STORE): bool;

    /**
     * Check if firstname validation is enabled.
     *
     * @param null|int|string|\Magento\Store\Api\Data\StoreInterface $store Store ID or store code or store object (optional)
     * @param string $scope Scope of the configuration value (optional)
     * @return bool True if enabled, false otherwise
     */
    public function isEnabledFirstname($store = null, string $scope = ScopeInterface::SCOPE_STORE): bool;

    /**
     * Check if lastname validation is enabled.
     *
     * @param null|int|string|\Magento\Store\Api\Data\StoreInterface $store Store ID or store code or store object (optional)
     * @param string $scope Scope of the configuration value (optional)
     * @return bool True if enabled, false otherwise
     */
    public function isEnabledLastname($store = null, string $scope = ScopeInterface::SCOPE_STORE): bool;

    /**
     * Check if street validation is enabled.
     *
     * @param null|int|string|\Magento\Store\Api\Data\StoreInterface $store Store ID or store code or store object (optional)
     * @param string $scope Scope of the configuration value (optional)
     * @return bool True if enabled, false otherwise
     */
    public function isEnabledStreet($store = null, string $scope = ScopeInterface::SCOPE_STORE): bool;

    /**
     * Get the regex pattern for validating firstname.
     *
     * @param null|int|string|\Magento\Store\Api\Data\StoreInterface $store Store ID or store code or store object (optional)
     * @param string $scope Scope of the configuration value (optional)
     * @return string Regex pattern for firstname validation
     */
    public function getFirstnameRegex($store = null, string $scope = ScopeInterface::SCOPE_STORE): string;

    /**
     * Get the regex pattern for validating lastname.
     *
     * @param null|int|string|\Magento\Store\Api\Data\StoreInterface $store Store ID or store code or store object (optional)
     * @param string $scope Scope of the configuration value (optional)
     * @return string Regex pattern for lastname validation
     */
    public function getLastnameRegex($store = null, string $scope = ScopeInterface::SCOPE_STORE): string;

    /**
     * Get the regex pattern for validating street.
     *
     * @param null|int|string|\Magento\Store\Api\Data\StoreInterface $store Store ID or store code or store object (optional)
     * @param string $scope Scope of the configuration value (optional)
     * @return string Regex pattern for street validation
     */
    public function getStreetRegex($store = null, string $scope = ScopeInterface::SCOPE_STORE): string;

    /**
     * Get the error message for invalid firstname.
     *
     * @param null|int|string|\Magento\Store\Api\Data\StoreInterface $store Store ID or store code or store object (optional)
     * @param string $scope Scope of the configuration value (optional)
     * @return string Error message for invalid firstname
     */
    public function getFirstnameErrorMessage($store = null, string $scope = ScopeInterface::SCOPE_STORE): string;

    /**
     * Get the error message for invalid lastname.
     *
     * @param null|int|string|\Magento\Store\Api\Data\StoreInterface $store Store ID or store code or store object (optional)
     * @param string $scope Scope of the configuration value (optional)
     * @return string Error message for invalid lastname
     */
    public function getLastnameErrorMessage($store = null, string $scope = ScopeInterface::SCOPE_STORE): string;

    /**
     * Get the error message for invalid street.
     *
     * @param null|int|string|\Magento\Store\Api\Data\StoreInterface $store Store ID or store code or store object (optional)
     * @param string $scope Scope of the configuration value (optional)
     * @return string Error message for invalid street
     */
    public function getStreetErrorMessage($store = null, string $scope = ScopeInterface::SCOPE_STORE): string;
}
