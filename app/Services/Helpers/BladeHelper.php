<?php

namespace App\Services\Helpers;
 
class BladeHelper {

    // public static $allowedDbActions = ['reset', 'prefix', 'repair', 'optimize'];

    public static function getPerformanceBackgroundClass( $score, $darker = false ) 
    {
        if ( !$score ) {
            return 'bg-gray-100';
        }

        if ($score <= 49) {
            if ( $darker ) {
                return 'bg-red-400 dark:bg-red-700';   
            }
            return 'bg-red-100 dark:bg-red-50/50';
        } elseif ($score <= 89) {
            if ( $darker ) {
                return 'bg-orange-400 dark:bg-orange-700';   
            }
            return 'bg-orange-100 dark:bg-orange-50/50';
        } else {
            if ( $darker ) {
                return 'bg-green-400 dark:bg-green-700';
            }
            return 'bg-green-100 dark:bg-green-950';
        }
    }
    /**
     * Helper method to highlight if the version of wordpress needs updateing
     * @todo use global enums somewhere.
     *
     * @param [type] $version
     * @return void
     */
    public static function getWPVersionBackgroundClass( $version ) 
    {
        if ( !$version ) {
            return 'bg-gray-100 dark:bg-custom-400/10';
        }

        if ($version <= 5) {
            return 'bg-red-100 dark:bg-custom-400/10';
        } elseif ($version <= 6) {
            return 'bg-orange-100 dark:bg-custom-400/10';
        } else {
            return 'bg-green-100 dark:bg-custom-400/10';
        }
    }

    public static function getPHPBackgroundClass( $version ) 
    {
        if ( !$version ) {
            return 'bg-gray-100 dark:bg-custom-400/10';
        }

        if ($version <= 7) {
            return 'bg-red-100 dark:bg-custom-400/10 dark:text-red-200';
        } elseif ($version <= 8) {
            return 'bg-orange-100 dark:bg-custom-400/10 dark:text-orange-200';
        } else {
            return 'bg-green-100 dark:bg-custom-400/10 dark:text-green-200';
        }
    }

    public static function getPerformanceTextClass($score) 
    {
        if ( !$score ) {
            return 'text-gray-600';
        }

        if ($score <= 49) {
            return 'text-red-600';
        } elseif ($score <= 89) {
            return 'text-orange-600';
        } else {
            return 'text-green-500';
        }
    }
 
}