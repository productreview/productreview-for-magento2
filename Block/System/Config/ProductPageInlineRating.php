<?php

namespace Productreview\Reviews\Block\System\Config;
 
use Magento\Framework\Option\ArrayInterface;
use Productreview\Reviews\Model\Settings;

class ProductPageInlineRating implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => Settings::PRODUCT_PAGE_INLINE_RATING_DISABLED,              'label' => __('Disabled')],
            ['value' => Settings::PRODUCT_PAGE_INLINE_RATING_ENABLED,               'label' => __('Enabled')],
            ['value' => Settings::PRODUCT_PAGE_INLINE_RATING_MANUAL_IMPLEMENTATION, 'label' => __('Manual Implementation')]
        ];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            Settings::PRODUCT_PAGE_INLINE_RATING_DISABLED              => __('Disabled'),
            Settings::PRODUCT_PAGE_INLINE_RATING_ENABLED               => __('Enabled'),
            Settings::PRODUCT_PAGE_INLINE_RATING_MANUAL_IMPLEMENTATION => __('Manual Implementation'),
        ];
    }
}