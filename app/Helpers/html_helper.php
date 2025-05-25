<?php

if (!function_exists('br')) {
    /**
     * Generates HTML line break tag
     *
     * @param int $count Number of times to repeat the tag
     * @return string
     */
    function br(int $count = 1): string
    {
        return str_repeat('<br />', $count);
    }
}

if (!function_exists('nbs')) {
    /**
     * Generates non-breaking space entities
     *
     * @param int $count Number of spaces to repeat
     * @return string
     */
    function nbs(int $count = 1): string
    {
        return str_repeat('&nbsp;', $count);
    }
}

if (!function_exists('heading')) {
    /**
     * Generates an HTML heading tag
     *
     * @param string $data Content
     * @param int $h Heading level (1-6)
     * @param string $attributes HTML attributes
     * @return string
     */
    function heading(string $data = '', int $h = 1, string $attributes = ''): string
    {
        $h = min(6, max(1, $h)); // Ensure h1-h6 only
        return "<h{$h}" . ($attributes ? " {$attributes}" : '') . ">{$data}</h{$h}>";
    }
} 