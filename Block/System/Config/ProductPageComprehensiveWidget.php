<?php
namespace Productreview\Reviews\Block\System\Config;
 
use Magento\Framework\Option\ArrayInterface;
use Productreview\Reviews\Model\Settings;

class ProductPageComprehensiveWidget implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => Settings::PRODUCT_PAGE_COMPREHENSIVE_WIDGET_DISABLED,              'label' => __('Disabled')],
            ['value' => Settings::PRODUCT_PAGE_COMPREHENSIVE_WIDGET_PAGE_FOOTER,           'label' => __('Page Footer')],
            ['value' => Settings::PRODUCT_PAGE_COMPREHENSIVE_WIDGET_TAB,                   'label' => __('Tab')],
            ['value' => Settings::PRODUCT_PAGE_COMPREHENSIVE_WIDGET_MANUAL_IMPLEMENTATION, 'label' => __('Manual Implementation')]
        ];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            Settings::PRODUCT_PAGE_COMPREHENSIVE_WIDGET_DISABLED              => __('Disabled'),
            Settings::PRODUCT_PAGE_COMPREHENSIVE_WIDGET_PAGE_FOOTER           => __('Page Footer'),
            Settings::PRODUCT_PAGE_COMPREHENSIVE_WIDGET_TAB                   => __('Tab'),
            Settings::PRODUCT_PAGE_COMPREHENSIVE_WIDGET_MANUAL_IMPLEMENTATION => __('Manual Implementation'),
        ];
    }
}