# Hryvinskyi_QuoteAddressValidator

## Overview

The `Hryvinskyi_QuoteAddressValidator` module provides validation for quote addresses in Magento 2. It allows administrators to configure regex patterns and error messages for validating various address fields such as lastname and street.

## Installation

1. **Composer Installation:**
   ```bash
   composer require hryvinskyi/magento2-quote-address-validator
    ```
2. **Enable the Module:**
    ```bash
    php bin/magento module:enable Hryvinskyi_QuoteAddressValidator
    ```
3. **Clear Cache:**
    ```bash
    php bin/magento cache:clean
    ```
   
## Configuration

1. Navigate to `Stores > Configuration > Hryvinskyi > Quote Address Validator.`
2. Configure the following fields:
    - **Enable:** Set to `Yes` to enable the module.
    - **Enable Firstname Validation** Set to `Yes` to enable validation for the firstname field.
    - **Firstname Regex** Enter a regex pattern to validate the firstname field.
    - **First Firstname Message** Enter an error message to display when the firstname field does not match the pattern.
    - **Enable Lastname Validation** Set to `Yes` to enable validation for the lastname field.
    - **Lastname Regex:** Enter a regex pattern to validate the lastname field.
    - **Lastname Error Message:** Enter an error message to display when the lastname field does not match the pattern.
    - **Enable Street Validation** Set to `Yes` to enable validation for the street field.
    - **Street Regex:** Enter a regex pattern to validate the street field.
    - **Street Error Message:** Enter an error message to display when the street field does not match the pattern.

## Command Line Interface

The module provides a command-line interface (CLI) to validate quote addresses. To use the CLI, run the following command:

```bash
    php bin/magento hryvinskyi:quote-address-validator:check-existing-addresses
```

This command will validate all existing quote addresses and display any errors that are found.
This is useful for detecting country-specific errors in addresses and names and changing Regex patterns for validation.

## Example Regex Patterns

 - Firstname Regex: `/^([\p{L}' ]{3,50})$/u`
 - Lastname Regex: `/^([\p{L}' ]{3,50})$/u`
 - Street Regex: `/^[\p{L}0-9\s,'\-\.#\/\\\\]*$/u`

## Explanation of Regex

 - `^` - Asserts position at the start of the string.
 - `[\p{L}0-9\s,'\-\.#\/\\\\]` Character class allowing:
    - **`\p{L}`** - in a regex pattern, it will match any character that is considered a letter in Unicode.
    - **`0-9`** - Any digit.
    - **`\s`** - Any whitespace character (spaces, tabs, line breaks)
    - **`,'`** - Comma, apostrophe and hyphen.
    - **`\-`** - Hyphen. (Note that the hyphen is escaped because it is a special character in regex.)
    - **`\.`** - Period. (Note that the dot is escaped because it is a special character in regex.)
    - **`#`** - Hash symbol.
    - **`\/`** - Forward slash. (Note that the slash is escaped because it is a special character in regex.)
    - **`\\\\`**  Backslash (the double backslash is necessary to escape the backslash itself in PHP strings and regex)
 - **`{3,50}`** - Match between 3 and 50 characters.
 - **`*`** - Matches zero or more occurrences of the preceding element.
 - **`$`** - End of the string.
 - **`u`** - modifier: Treats the pattern as UTF-8, necessary for proper Unicode matching.


## Notes

Preference for `\Magento\Quote\Model\BillingAddressManagement` added only for correct error message display. (added `LocalizedException` catch to `assign` method)
