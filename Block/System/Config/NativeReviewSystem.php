<?php
namespace Productreview\Reviews\Block\System\Config;
 
use Magento\Framework\Option\ArrayInterface;
use Productreview\Reviews\Model\Settings;

class NativeReviewSystem implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => Settings::NATIVE_REVIEW_SYSTEM_DISABLED, 'label' => __('Disabled')],
            ['value' => Settings::NATIVE_REVIEW_SYSTEM_ENABLED,  'label' => __('Enabled')],
        ];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            Settings::NATIVE_REVIEW_SYSTEM_DISABLED => __('Disabled'),
            Settings::NATIVE_REVIEW_SYSTEM_ENABLED  => __('Enabled'),
        ];
    }
}