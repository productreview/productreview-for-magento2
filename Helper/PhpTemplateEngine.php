<?php

namespace Productreview\Reviews\Helper;

class PhpTemplateEngine
{
    static public function render(callable $fn)
    {
        ob_start();
        $fn();

        return ob_get_clean() ?: '';
    }
}
