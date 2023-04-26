<?php

namespace Fahim\ZipCodeValidator\Model\Config\Source;

class Apply implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Retrieve Option Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '0', 'label' => __('Apply to Individual Products')],
            ['value' => '1', 'label' => __('Apply to all Products')]
        ];
    }
}
