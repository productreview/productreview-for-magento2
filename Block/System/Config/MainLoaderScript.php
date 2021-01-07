<?php
namespace Productreview\Reviews\Block\System\Config;
 
use Magento\Framework\Option\ArrayInterface;
use Productreview\Reviews\Model\Settings;

class MainLoaderScript implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => Settings::MAIN_LOADER_SCRIPT_ENABLED,               'label' => __('Enabled')],
            ['value' => Settings::MAIN_LOADER_SCRIPT_MANUAL_IMPLEMENTATION, 'label' => __('Manual Implementation')],
        ];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            Settings::MAIN_LOADER_SCRIPT_ENABLED               => __('Enabled'),
            Settings::MAIN_LOADER_SCRIPT_MANUAL_IMPLEMENTATION => __('Manual Implementation'),
        ];
    }
}