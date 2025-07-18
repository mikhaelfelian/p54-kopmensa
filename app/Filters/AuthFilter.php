<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use IonAuth\Libraries\IonAuth;
use Config\Services;

class AuthFilter implements FilterInterface
{
    protected $ionAuth;
    protected $session;

    public function __construct()
    {
        $this->session = Services::session();
        $this->ionAuth = new IonAuth();
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        if (!$this->ionAuth->loggedIn()) {
            // Handle AJAX requests differently
            if ($request->isAJAX()) {
                return Services::response()
                    ->setStatusCode(401)
                    ->setJSON(['error' => 'Unauthorized', 'message' => 'Silakan login terlebih dahulu!']);
            }
            
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('auth/login'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
} 