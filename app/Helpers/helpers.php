<?php

if (!function_exists('system_setting')) {
    /**
     * Get or set system setting value
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function system_setting($key, $default = null)
    {
        return \App\Models\Setting::get($key, $default);
    }
}
