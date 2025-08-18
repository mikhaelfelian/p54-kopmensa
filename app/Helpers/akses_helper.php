<?php

/**
 * Access Control Helper based on Ion Auth
 * 
 * @author Mikhael Felian Waskito
 * @version 1.0
 */

if (!function_exists('akses_root')) {
    function akses_root()
    {
        $ionAuth = new \IonAuth\Libraries\IonAuth();
        
        if (!$ionAuth->loggedIn()) {
            return false;
        }
        
        $user = $ionAuth->user()->row();
        if (!$user) {
            return false;
        }
        
        return $ionAuth->inGroup(1, $user->id);
    }
}

if (!function_exists('akses_superadmin')) {
    function akses_superadmin()
    {
        $ionAuth = new \IonAuth\Libraries\IonAuth();
        
        if (!$ionAuth->loggedIn()) {
            return false;
        }
        
        $user = $ionAuth->user()->row();
        if (!$user) {
            return false;
        }
        
        return $ionAuth->inGroup(2, $user->id);
    }
}

if (!function_exists('akses_manager')) {
    function akses_manager()
    {
        $ionAuth = new \IonAuth\Libraries\IonAuth();
        
        if (!$ionAuth->loggedIn()) {
            return false;
        }
        
        $user = $ionAuth->user()->row();
        if (!$user) {
            return false;
        }
        
        return $ionAuth->inGroup(3, $user->id);
    }
}

if (!function_exists('akses_admin')) {
    function akses_admin()
    {
        $ionAuth = new \IonAuth\Libraries\IonAuth();
        
        if (!$ionAuth->loggedIn()) {
            return false;
        }
        
        $user = $ionAuth->user()->row();
        if (!$user) {
            return false;
        }
        
        return $ionAuth->inGroup(4, $user->id);
    }
}

if (!function_exists('akses_kasir')) {
    function akses_kasir()
    {
        $ionAuth = new \IonAuth\Libraries\IonAuth();
        
        if (!$ionAuth->loggedIn()) {
            return false;
        }
        
        $user = $ionAuth->user()->row();
        if (!$user) {
            return false;
        }
        
        // Debug: Check what groups the user is in
        $groups = $ionAuth->getUsersGroups($user->id);
        $groupIds = [];
        $groupNames = [];
        
        if ($groups && method_exists($groups, 'result')) {
            $groupResults = $groups->result();
            foreach ($groupResults as $group) {
                $groupIds[] = $group->id;
                $groupNames[] = $group->name;
            }
        } elseif ($groups && method_exists($groups, 'getResult')) {
            $groupResults = $groups->getResult();
            foreach ($groupResults as $group) {
                $groupIds[] = $group->id;
                $groupNames[] = $group->name;
            }
        } elseif (is_array($groups)) {
            foreach ($groups as $group) {
                $groupIds[] = $group->id;
                $groupNames[] = $group->name;
            }
        }
        
        // Check if user is in kasir group (try different possible IDs)
        return $ionAuth->inGroup(5, $user->id) || 
               $ionAuth->inGroup('5', $user->id) || 
               $ionAuth->inGroup('kasir', $user->id) ||
               in_array('kasir', array_map('strtolower', $groupNames));
    }
}

if (!function_exists('get_user_role')) {
    function get_user_role()
    {
        $ionAuth = new \IonAuth\Libraries\IonAuth();
        
        if (!$ionAuth->loggedIn()) {
            return 'guest';
        }
        
        $user = $ionAuth->user()->row();
        if (!$user) {
            return 'guest';
        }
        
        $groups = $ionAuth->getUsersGroups($user->id);
        $groupResults = [];
        
        if ($groups && method_exists($groups, 'result')) {
            $groupResults = $groups->result();
        } elseif ($groups && method_exists($groups, 'getResult')) {
            $groupResults = $groups->getResult();
        } elseif (is_array($groups)) {
            $groupResults = $groups;
        }
        
        if (empty($groupResults)) {
            return 'user';
        }
        
        $highestRole = 'user';
        $lowestGroupId = 999;
        
        foreach ($groupResults as $group) {
            if ($group->id < $lowestGroupId) {
                $lowestGroupId = $group->id;
                $highestRole = $group->name;
            }
        }
        
        return strtolower($highestRole);
    }
}

if (!function_exists('check_akses')) {
    function check_akses($role)
    {
        switch (strtolower($role)) {
            case 'root':
                return akses_root();
            case 'superadmin':
                return akses_superadmin();
            case 'manager':
                return akses_manager();
            case 'admin':
                return akses_admin();
            case 'kasir':
                return akses_kasir();
            default:
                return false;
        }
    }
}

if (!function_exists('require_akses')) {
    function require_akses($role, $redirect_url = null)
    {
        if (!check_akses($role)) {
            if ($redirect_url === null) {
                $redirect_url = base_url('auth/login');
            }
            
            session()->setFlashdata('error', 'Anda tidak memiliki akses ke halaman ini.');
            
            header('Location: ' . $redirect_url);
            exit;
        }
    }
}

// Debug function to see user groups
if (!function_exists('debug_user_groups')) {
    function debug_user_groups()
    {
        $ionAuth = new \IonAuth\Libraries\IonAuth();
        
        if (!$ionAuth->loggedIn()) {
            return 'Not logged in';
        }
        
        $user = $ionAuth->user()->row();
        if (!$user) {
            return 'User not found';
        }
        
        $groups = $ionAuth->getUsersGroups($user->id);
        $groupInfo = [];
        
        if ($groups && method_exists($groups, 'result')) {
            $groupResults = $groups->result();
        } elseif ($groups && method_exists($groups, 'getResult')) {
            $groupResults = $groups->getResult();
        } elseif (is_array($groups)) {
            $groupResults = $groups;
        } else {
            $groupResults = [];
        }
        
        foreach ($groupResults as $group) {
            $groupInfo[] = "ID: {$group->id}, Name: {$group->name}";
        }
        
        return [
            'user_id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'groups' => $groupInfo
        ];
    }
}

// Hierarchical access functions (if you need them)
if (!function_exists('akses_superadmin_or_higher')) {
    function akses_superadmin_or_higher()
    {
        return akses_root() || akses_superadmin();
    }
}

if (!function_exists('akses_manager_or_higher')) {
    function akses_manager_or_higher()
    {
        return akses_root() || akses_superadmin() || akses_manager();
    }
}

if (!function_exists('akses_admin_or_higher')) {
    function akses_admin_or_higher()
    {
        return akses_root() || akses_superadmin() || akses_manager() || akses_admin();
    }
}

if (!function_exists('akses_kasir_or_higher')) {
    function akses_kasir_or_higher()
    {
        return akses_root() || akses_superadmin() || akses_manager() || akses_admin() || akses_kasir();
    }
}
