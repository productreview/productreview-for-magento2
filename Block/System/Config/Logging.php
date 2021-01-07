<?php

namespace Productreview\Reviews\Block\System\Config;
 
use Magento\Framework\Option\ArrayInterface;
use Productreview\Reviews\Model\Settings;

class Logging implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => Settings::LOGGING_DISABLED,  'label' => __('Disabled')],
            ['value' => Settings::LOGGING_EMERGENCY, 'label' => __('Emergency')],
            ['value' => Settings::LOGGING_ALERT,     'label' => __('Alert')],
            ['value' => Settings::LOGGING_CRITICAL,  'label' => __('Critical')],
            ['value' => Settings::LOGGING_ERROR,     'label' => __('Error')],
            ['value' => Settings::LOGGING_WARNING,   'label' => __('Warning')],
            ['value' => Settings::LOGGING_NOTICE,    'label' => __('Notice')],
            ['value' => Settings::LOGGING_INFO,      'label' => __('Info')],
            ['value' => Settings::LOGGING_DEBUG,     'label' => __('Debug')],
        ];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            Settings::LOGGING_DISABLED  => __('Disabled'),
            Settings::LOGGING_EMERGENCY => __('Emergency'),
            Settings::LOGGING_ALERT     => __('Alert'),
            Settings::LOGGING_CRITICAL  => __('Critical'),
            Settings::LOGGING_ERROR     => __('Error'),
            Settings::LOGGING_WARNING   => __('Warning'),
            Settings::LOGGING_NOTICE    => __('Notice'),
            Settings::LOGGING_INFO      => __('Info'),
            Settings::LOGGING_DEBUG     => __('Debug'),
        ];
    }
}