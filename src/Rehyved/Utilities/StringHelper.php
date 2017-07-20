<?php

namespace Rehyved\Utilities;


class StringHelper
{
    public static function endsWith(string $haystack, string $needle, bool $caseInsensitive = false): bool
    {
        $haystackLength = strlen($haystack);
        $needleLength = strlen($needle);
        if ($needleLength > $haystackLength) return false;
        return substr_compare($haystack, $needle, $haystackLength - $needleLength, $needleLength, $caseInsensitive) === 0;
    }

    public static function startsWith($haystack, $needle, bool $caseInsensitive = false) : bool
    {
        $haystackLength = strlen($haystack);
        $needleLength = strlen($needle);
        if ($needleLength > $haystackLength) return false;
        return substr_compare($haystack, $needle, 0, $needleLength, $caseInsensitive) === 0;
    }

    public static function equals($first, $second, bool $caseInsensitive = false) : bool
    {
        if (!$caseInsensitive) {
            return $first === $second;
        } else {
            return mb_strtolower($first) === mb_strtolower($second);
        }
    }

    public static function contains($haystack, $needle, bool $caseInsensitive = false) : bool
    {
        if ($caseInsensitive) {
            return stripos($haystack, $needle) !== false;
        }
        return strpos($haystack, $needle) !== false;
    }
}