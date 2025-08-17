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
        $ionAuth = \IonAuth\Libraries\IonAuth::getInstance();
        
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
        $ionAuth = \IonAuth\Libraries\IonAuth::getInstance();
        
        if (!$ionAuth->loggedIn()) {
            return false;
        }
        
        $user = $ionAuth->user()->row();
        if (!$user) {
            return false;
        }
        
        return $ionAuth->inGroup(2, $user->id) || akses_root();
    }
}

if (!function_exists('akses_manager')) {
    function akses_manager()
    {
        $ionAuth = \IonAuth\Libraries\IonAuth::getInstance();
        
        if (!$ionAuth->loggedIn()) {
            return false;
        }
        
        $user = $ionAuth->user()->row();
        if (!$user) {
            return false;
        }
        
        return $ionAuth->inGroup(3, $user->id) || akses_superadmin();
    }
}

if (!function_exists('akses_admin')) {
    function akses_admin()
    {
        $ionAuth = \IonAuth\Libraries\IonAuth::getInstance();
        
        if (!$ionAuth->loggedIn()) {
            return false;
        }
        
        $user = $ionAuth->user()->row();
        if (!$user) {
            return false;
        }
        
        return $ionAuth->inGroup(4, $user->id) || akses_manager();
    }
}

if (!function_exists('akses_kasir')) {
    function akses_kasir()
    {
        $ionAuth = \IonAuth\Libraries\IonAuth::getInstance();
        
        if (!$ionAuth->loggedIn()) {
            return false;
        }
        
        $user = $ionAuth->user()->row();
        if (!$user) {
            return false;
        }
        
        return $ionAuth->inGroup(5, $user->id) || akses_admin();
    }
}

if (!function_exists('get_user_role')) {
    function get_user_role()
    {
        $ionAuth = \IonAuth\Libraries\IonAuth::getInstance();
        
        if (!$ionAuth->loggedIn()) {
            return 'guest';
        }
        
        $user = $ionAuth->user()->row();
        if (!$user) {
            return 'guest';
        }
        
        $groups = $ionAuth->getUsersGroups($user->id)->result();
        
        if (empty($groups)) {
            return 'user';
        }
        
        $highestRole = 'user';
        $lowestGroupId = 999;
        
        foreach ($groups as $group) {
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
