<?php

namespace Hryvinskyi\QuoteAddressValidator\Model\Validation;

use Hryvinskyi\QuoteAddressValidator\Model\ConfigInterface;

abstract class AbstractValidator implements \Hryvinskyi\QuoteAddressValidator\Model\ValidationInterface
{
    private ConfigInterface $config;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @return ConfigInterface
     */
    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    /**
     * Check string for stop words
     *
     * @param string $string
     * @param array $stopWords
     * @return bool True if the string does not contain stop words
     */
    public function checkStringForStopWords(string $string, array $stopWords): bool
    {
        foreach ($stopWords as $stopWord) {
            if (stripos($string, $stopWord) !== false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate the value
     *
     * @param string $value
     * @param string $pattern
     * @param array $stopWords
     * @return bool True if the value is valid
     */
    public function validate(string $value, string $pattern, array $stopWords): bool
    {
        switch ($this->getConfig()->getValidationType()) {
            case 1: // Regex
                return preg_match($pattern, $value) === 1;
                break;
            case 2: // Stop words
                return $this->checkStringForStopWords($value, $stopWords);
                break;
            case 3: // Regex and stop words
                return preg_match($pattern, $value) === 1 && $this->checkStringForStopWords($value, $stopWords);
                break;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    abstract public function execute(\Magento\Quote\Api\Data\AddressInterface $address): bool;
}
