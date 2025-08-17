<?php

if (!function_exists('can')) {
    /**
     * Check if current user has permission for specific action on module
     * 
     * @param string $action Action name (create, read, update, delete, etc.)
     * @param string $moduleRoute Module route (e.g., 'Master/Item')
     * @return bool
     */
    function can($action, $moduleRoute = null)
    {
        $permissionService = service('permission');
        return $permissionService->can($action, $moduleRoute);
    }
}

if (!function_exists('canCreate')) {
    /**
     * Check if current user can create in module
     */
    function canCreate($moduleRoute = null)
    {
        return can('create', $moduleRoute);
    }
}

if (!function_exists('canRead')) {
    /**
     * Check if current user can read in module
     */
    function canRead($moduleRoute = null)
    {
        return can('read', $moduleRoute);
    }
}

if (!function_exists('canReadAll')) {
    /**
     * Check if current user can read all records in module
     */
    function canReadAll($moduleRoute = null)
    {
        return can('read_all', $moduleRoute);
    }
}

if (!function_exists('canUpdate')) {
    /**
     * Check if current user can update in module
     */
    function canUpdate($moduleRoute = null)
    {
        return can('update', $moduleRoute);
    }
}

if (!function_exists('canUpdateAll')) {
    /**
     * Check if current user can update all records in module
     */
    function canUpdateAll($moduleRoute = null)
    {
        return can('update_all', $moduleRoute);
    }
}

if (!function_exists('canDelete')) {
    /**
     * Check if current user can delete in module
     */
    function canDelete($moduleRoute = null)
    {
        return can('delete', $moduleRoute);
    }
}

if (!function_exists('canDeleteAll')) {
    /**
     * Check if current user can delete all records in module
     */
    function canDeleteAll($moduleRoute = null)
    {
        return can('delete_all', $moduleRoute);
    }
}

if (!function_exists('canExport')) {
    /**
     * Check if current user can export from module
     */
    function canExport($moduleRoute = null)
    {
        return can('export', $moduleRoute);
    }
}

if (!function_exists('canImport')) {
    /**
     * Check if current user can import to module
     */
    function canImport($moduleRoute = null)
    {
        return can('import', $moduleRoute);
    }
}

if (!function_exists('canApprove')) {
    /**
     * Check if current user can approve in module
     */
    function canApprove($moduleRoute = null)
    {
        return can('approve', $moduleRoute);
    }
}

if (!function_exists('canReject')) {
    /**
     * Check if current user can reject in module
     */
    function canReject($moduleRoute = null)
    {
        return can('reject', $moduleRoute);
    }
}

if (!function_exists('isAdmin')) {
    /**
     * Check if current user is admin
     */
    function isAdmin()
    {
        $permissionService = service('permission');
        return $permissionService->isAdmin();
    }
}

if (!function_exists('showIfCan')) {
    /**
     * Show HTML content only if user has permission
     * 
     * @param string $action Action name
     * @param string $moduleRoute Module route
     * @param string $html HTML content to show
     * @return string
     */
    function showIfCan($action, $moduleRoute, $html)
    {
        return can($action, $moduleRoute) ? $html : '';
    }
}

if (!function_exists('hideIfCannot')) {
    /**
     * Hide HTML content if user doesn't have permission
     * 
     * @param string $action Action name
     * @param string $moduleRoute Module route
     * @param string $html HTML content to show
     * @return string
     */
    function hideIfCannot($action, $moduleRoute, $html)
    {
        return can($action, $moduleRoute) ? $html : '<!-- Hidden due to insufficient permissions -->';
    }
}

if (!function_exists('permissionButton')) {
    /**
     * Generate a button only if user has permission
     * 
     * @param string $action Action name
     * @param string $moduleRoute Module route
     * @param string $text Button text
     * @param string $class CSS classes
     * @param string $onclick Onclick event
     * @return string
     */
    function permissionButton($action, $moduleRoute, $text, $class = 'btn btn-primary', $onclick = '')
    {
        if (!can($action, $moduleRoute)) {
            return '';
        }
        
        $onclickAttr = $onclick ? " onclick=\"$onclick\"" : '';
        return "<button type=\"button\" class=\"$class\"$onclickAttr>$text</button>";
    }
}

if (!function_exists('permissionLink')) {
    /**
     * Generate a link only if user has permission
     * 
     * @param string $action Action name
     * @param string $moduleRoute Module route
     * @param string $text Link text
     * @param string $url URL
     * @param string $class CSS classes
     * @return string
     */
    function permissionLink($action, $moduleRoute, $text, $url, $class = '')
    {
        if (!can($action, $moduleRoute)) {
            return '';
        }
        
        $classAttr = $class ? " class=\"$class\"" : '';
        return "<a href=\"$url\"$classAttr>$text</a>";
    }
}
