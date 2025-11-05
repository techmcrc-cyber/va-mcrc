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

if (!function_exists('get_country_codes')) {
    /**
     * Get all country calling codes
     * 
     * @return array
     */
    function get_country_codes()
    {
        return config('countries.calling_codes', []);
    }
}

if (!function_exists('get_default_country_code')) {
    /**
     * Get default country calling code
     * 
     * @return string
     */
    function get_default_country_code()
    {
        return config('countries.default_code', '+91');
    }
}

if (!function_exists('render_country_code_options')) {
    /**
     * Render country code dropdown options
     * 
     * @param string|null $selected The selected country code
     * @return string HTML options
     */
    function render_country_code_options($selected = null)
    {
        $countryCodes = get_country_codes();
        $defaultCode = get_default_country_code();
        $selected = $selected ?? $defaultCode;
        
        $options = '';
        foreach ($countryCodes as $code => $country) {
            $isSelected = ($code === $selected) ? 'selected' : '';
            $options .= sprintf(
                '<option value="%s" %s>%s (%s)</option>',
                htmlspecialchars($code),
                $isSelected,
                htmlspecialchars($code),
                htmlspecialchars($country)
            );
        }
        
        return $options;
    }
}


if (!function_exists('format_whatsapp_number')) {
    /**
     * Format WhatsApp number with country code
     * 
     * @param string|null $countryCode
     * @param string|null $number
     * @return string
     */
    function format_whatsapp_number($countryCode, $number)
    {
        if (empty($number)) {
            return '';
        }
        
        $code = $countryCode ?? '+91';
        return $code . ' ' . $number;
    }
}
