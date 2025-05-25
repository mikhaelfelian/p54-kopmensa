<?php

if (!function_exists('get_active_theme')) {
    function get_active_theme() {
        $db = \Config\Database::connect();
        $theme = $db->table('tbl_pengaturan_theme')
                   ->where('status', 1)
                   ->get()
                   ->getRow();
                   
        return $theme ? $theme->path : 'admin-lte-3'; // default theme if none active
    }
}

if (!function_exists('theme_path')) {
    function theme_path($view) {
        return get_active_theme() . '/layout/' . $view;
    }
} 