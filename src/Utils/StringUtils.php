<?php
namespace Acciona\Users\Utils;

/**
 * Utility functions for string manipulations
 *
 * @author Danilo Dominguez Perez
 */
class StringUtils
{
    public static function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    public static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }
}