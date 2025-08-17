<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class TestAkses extends Controller
{
    public function index()
    {
        // Test the access control functions
        $data = [
            'title' => 'Access Control Test',
            'user_role' => get_user_role(),
            'access_levels' => [
                'root' => akses_root(),
                'superadmin' => akses_superadmin(),
                'manager' => akses_manager(),
                'admin' => akses_admin(),
                'kasir' => akses_kasir()
            ]
        ];
        
        return view('test_akses', $data);
    }
    
    public function adminOnly()
    {
        // This will redirect if user doesn't have admin access
        require_akses('admin');
        
        return "You have admin access!";
    }
    
    public function managerOnly()
    {
        // This will redirect if user doesn't have manager access
        require_akses('manager');
        
        return "You have manager access!";
    }
    
    public function kasirOnly()
    {
        // This will redirect if user doesn't have kasir access
        require_akses('kasir');
        
        return "You have kasir access!";
    }
}
