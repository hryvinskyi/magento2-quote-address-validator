<?php

namespace Hryvinskyi\QuoteAddressValidator\Model\Config\Source;

class ValidationType implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('Validate by Regex')],
            ['value' => '2', 'label' => __('Validate by stopwords')],
            ['value' => '3', 'label' => __('Validate by stopwords and regex')],
        ];
    }
}
