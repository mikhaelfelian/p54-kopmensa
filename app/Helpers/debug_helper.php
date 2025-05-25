<?php

if (!function_exists('pre')) {
    /**
     * Print data in a preformatted way
     * 
     * @param mixed $data Data to print
     * @param bool $die Stop execution after printing
     * @return void
     */
    function pre($data, bool $die = false) 
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        
        if ($die) die();
    }
}

if (!function_exists('dump')) {
    /**
     * Dump variable information
     * 
     * @param mixed $data Data to dump
     * @param bool $die Stop execution after dumping
     * @return void
     */
    function dump($data, bool $die = true)
    {
        echo "<pre style='background: #f4f4f4; padding: 15px; border: 1px solid #ddd; border-radius: 4px; margin: 10px 0; font-family: monospace;'>";
        var_dump($data);
        echo "</pre>";
        
        if ($die) die();
    }
}

if (!function_exists('dd')) {
    /**
     * Dump and die
     * 
     * @param mixed ...$args Variables to dump
     * @return void
     */
    function dd(...$args)
    {
        foreach ($args as $x) {
            dump($x, false);
        }
        die(1);
    }
} 