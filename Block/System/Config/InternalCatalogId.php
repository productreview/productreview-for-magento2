<?php
namespace Productreview\Reviews\Block\System\Config;

use Magento\Framework\Option\ArrayInterface;

class InternalCatalogId implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 'au', 'label' => __('Australia (AUD)')]];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return ['au' => __('Australia (AUD)')];
    }
}
