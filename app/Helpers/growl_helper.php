<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-05-28
 * This file represents the growl notification helper.
 */

if (!function_exists('growl_show')) {
    /**
     * Show growl notification
     * 
     * @param string $message Message to display
     * @param string $type Type of notification (success, error, warning, info)
     * @param string $title Optional title
     * @return string JavaScript code for growl
     */
    function growl_show($message = null, $type = "success", $title = "") 
    {
        if ($message) {
            $growl = "<!-- Growl JS Tampil disini -->";
            $growl .= "<script>
                $.growl({
                    message: '" . $message . "',
                    title: '" . $title . "',
                    type: '" . $type . "',
                    delay: 5000,
                    allow_dismiss: true,
                    offset: {
                        x: 20,
                        y: 85
                    },
                    spacing: 10,
                    z_index: 1031,
                    animate: {
                        enter: 'animated fadeInRight',
                        exit: 'animated fadeOutRight'
                    },
                    icon_type: 'class',
                    template: '<div data-growl=\"container\" class=\"alert\" role=\"alert\">' +
                        '<button type=\"button\" class=\"close\" data-growl=\"dismiss\">' +
                        '<span aria-hidden=\"true\">&times;</span>' +
                        '<span class=\"sr-only\">Close</span>' +
                        '</button>' +
                        '<span data-growl=\"icon\"></span>' +
                        '<span data-growl=\"title\"></span>' +
                        '<span data-growl=\"message\"></span>' +
                        '<a href=\"#\" class=\"alert-link\" data-growl=\"url\"></a>' +
                        '</div>'
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
        return growl_show($message, "error", $title);
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