<?php

if (!function_exists('toast_show')) {
    /**
     * Show toastr notification
     * 
     * @param string $message Message to display
     * @param string $type Type of notification (success, error, warning, info)
     * @param string $title Optional title
     * @return string JavaScript code for toastr
     */
    function toast_show($message = null, $type = "success", $title = "") 
    {
        if ($message) {
            $toastr = "<!-- Toastr JS Tampil disini -->";
            $toastr .= "<script>
                toastr.options = {
                    'closeButton': true,
                    'debug': false,
                    'newestOnTop': true,
                    'progressBar': true,
                    'positionClass': 'toast-top-right',
                    'preventDuplicates': false,
                    'onclick': null,
                    'showDuration': '300',
                    'hideDuration': '1000',
                    'timeOut': '5000',
                    'extendedTimeOut': '1000',
                    'showEasing': 'swing',
                    'hideEasing': 'linear',
                    'showMethod': 'fadeIn',
                    'hideMethod': 'fadeOut'
                };
                toastr." . $type . "('" . $message . "', '" . $title . "');
            </script>";

            return $toastr;
        }
    }
}

if (!function_exists('toast_success')) {
    function toast_success($message, $title = "") {
        return toast_show($message, "success", $title);
    }
}

if (!function_exists('toast_error')) {
    function toast_error($message, $title = "") {
        return toast_show($message, "error", $title);
    }
}

if (!function_exists('toast_warning')) {
    function toast_warning($message, $title = "") {
        return toast_show($message, "warning", $title);
    }
}

if (!function_exists('toast_info')) {
    function toast_info($message, $title = "") {
        return toast_show($message, "info", $title);
    }
}