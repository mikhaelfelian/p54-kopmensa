<?php

if (!function_exists('format_angka_db')) {
    function format_angka_db($angka) {
        // Remove thousand separator and change decimal point
        $angka = str_replace('.', '', $angka); // Remove thousand separator
        $angka = str_replace(',', '.', $angka); // Change decimal separator to dot
        return (float) $angka;
    }
} 