<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-05-28
 * This file represents the growl notification helper (now using Gritter for Quirk theme).
 */

if (!function_exists('growl_show')) {
    /**
     * Show Gritter notification (Quirk style)
     * 
     * @param string $message Message to display
     * @param string $type Type of notification (success, danger, warning, info)
     * @param string $title Optional title
     * @return string JavaScript code for Gritter
     */
    function growl_show($message = null, $type = "success", $title = "") 
    {
        if ($message) {
            // Map type to icon/class
            $iconClass = 'info-circle primary';
            switch ($type) {
                case 'success':
                    $iconClass = 'check-circle success';
                    break;
                case 'danger':
                case 'error':
                    $iconClass = 'times-circle danger';
                    break;
                case 'warning':
                    $iconClass = 'exclamation-circle warning';
                    break;
                case 'info':
                default:
                    $iconClass = 'info-circle primary';
                    break;
            }
            $growl = "<!-- Gritter JS Notification -->";
            $growl .= "<script>
                $.gritter.add({
                    title: '" . addslashes($title) . "',
                    text: '" . addslashes($message) . "',
                    class_name: 'with-icon " . $iconClass . "',
                    time: 5000,
                    sticky: false
                });
            </script>";
            return $growl;
        }
    }
}

if (!function_exists('growl_success')) {
    function growl_success($message, $title = "") {
        return growl_show($message, "success", $title);
    }
}

if (!function_exists('growl_error')) {
    function growl_error($message, $title = "") {
        return growl_show($message, "danger", $title);
    }
}

if (!function_exists('growl_warning')) {
    function growl_warning($message, $title = "") {
        return growl_show($message, "warning", $title);
    }
}

if (!function_exists('growl_info')) {
    function growl_info($message, $title = "") {
        return growl_show($message, "info", $title);
    }
}