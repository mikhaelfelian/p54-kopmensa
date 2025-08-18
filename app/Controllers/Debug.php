<?php

namespace App\Controllers;

class Debug extends BaseController
{
    public function index()
    {
        // Check if user is logged in
        if (!$this->ionAuth->loggedIn()) {
            return "Not logged in";
        }
        
        $user = $this->ionAuth->user()->row();
        if (!$user) {
            return "User not found";
        }
        
        $groups = $this->ionAuth->getUsersGroups($user->id);
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
        
        $debugInfo = [
            'user_id'                => $user->id,
            'username'               => $user->username,
            'email'                  => $user->email,
            'groups'                 => $groupInfo,
            'akses_kasir_result'     => akses_kasir(),
            'akses_admin_result'     => akses_admin(),
            'akses_superadmin_result'=> akses_superadmin(),
            'akses_manager_result'   => akses_manager(),
            'akses_root_result'      => akses_root(),
        ];
        
        echo "<pre>";
        print_r($debugInfo);
        echo "</pre>";
        
        // Also test the debug function from helper
        echo "<h3>From Helper Function:</h3>";
        echo "<pre>";
        print_r(debug_user_groups());
        echo "</pre>";
    }
}
