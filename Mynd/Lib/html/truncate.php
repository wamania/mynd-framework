<?php

/**
 * If <var>$text</var> is longer than <var>$length</var>, <var>$text</var> will
 * be truncated to the length of <var>$length</var> and the last three characters
 * will be replaced with the <var>$truncate_string</var>.
 */
function truncate($text, $length = 30, $truncate_string = '...')
{
    if (utf8_strlen($text) > $length) {
        return utf8_substr_replace($text, $truncate_string, $length - utf8_strlen($truncate_string));
    } else {
        return $text;
    }
}