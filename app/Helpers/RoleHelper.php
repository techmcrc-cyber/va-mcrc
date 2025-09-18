<?php

namespace App\Helpers;

class RoleHelper
{
    /**
     * Generate a consistent color for a role name
     * 
     * @param string $roleName
     * @return array [background, text, border]
     */
    public static function getRoleColors($roleName)
    {
        // Predefined colors for common roles
        $presetColors = [
            'super admin' => ['#4e73df', '#ffffff', '#224abe'],
            'admin' => ['#2c5282', '#ffffff', '#2b6cb0'],
            'editor' => ['#2f855a', '#ffffff', '#38a169'],
            'author' => ['#b7791f', '#ffffff', '#d69e2e'],
            'subscriber' => ['#c53030', '#ffffff', '#e53e3e'],
            'user' => ['#4a5568', '#ffffff', '#718096'],
        ];

        $roleLower = strtolower($roleName);
        
        // Return preset color if exists
        if (isset($presetColors[$roleLower])) {
            return $presetColors[$roleLower];
        }

        // Generate a consistent color based on role name
        $hash = md5($roleName);
        
        // Generate vibrant but slightly darker colors for better visibility
        $h = hexdec(substr($hash, 0, 2)) % 360;
        $s = 70 + (hexdec(substr($hash, 2, 2)) % 30); // 70-100%
        $l = 40 + (hexdec(substr($hash, 4, 2)) % 30);  // 40-70%
        
        $bgColor = self::hslToHex($h, $s, $l);
        
        // Slightly darker border color
        $borderColor = self::hslToHex($h, $s, $l - 10);
        
        // Determine text color based on brightness
        $textColor = (($h > 45 && $h < 210) || $l < 40) ? '#ffffff' : '#212529';
        
        return [$bgColor, $textColor, $borderColor];
    }
    
    /**
     * Convert HSL to HEX color
     */
    private static function hslToHex($h, $s, $l) 
    {
        $h /= 360;
        $s /= 100;
        $l /= 100;
        
        $r = $l;
        $g = $l;
        $b = $l;
        
        $v = ($l <= 0.5) ? ($l * (1.0 + $s)) : ($l + $s - $l * $s);
        
        if ($v > 0) {
            $m = $l + $l - $v;
            $sv = ($v - $m) / $v;
            $h *= 6.0;
            
            $sextant = floor($h);
            $fract = $h - $sextant;
            $vsf = $v * $sv * $fract;
            $mid1 = $m + $vsf;
            $mid2 = $v - $vsf;
            
            switch ($sextant) {
                case 0:
                    $r = $v;
                    $g = $mid1;
                    $b = $m;
                    break;
                case 1:
                    $r = $mid2;
                    $g = $v;
                    $b = $m;
                    break;
                case 2:
                    $r = $m;
                    $g = $v;
                    $b = $mid1;
                    break;
                case 3:
                    $r = $m;
                    $g = $mid2;
                    $b = $v;
                    break;
                case 4:
                    $r = $mid1;
                    $g = $m;
                    $b = $v;
                    break;
                case 5:
                    $r = $v;
                    $g = $m;
                    $b = $mid2;
                    break;
            }
        }
        
        $r = round($r * 255);
        $g = round($g * 255);
        $b = round($b * 255);
        
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
}
