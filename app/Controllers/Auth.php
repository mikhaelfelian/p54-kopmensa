<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * GitHub: https://github.com/mikhaelfelian
 * 2025-01-12
 * 
 * Auth Controller
 */

namespace App\Controllers;

use ReCaptcha\ReCaptcha;

class Auth extends BaseController
{
    protected $recaptcha;
    
    public function __construct()
    {
        $recaptchaModel = new \App\Models\ReCaptchaModel();
        $this->recaptcha = new ReCaptcha($recaptchaModel->getSecretKey());
    }

    public function index()
    {
        $data = [
            'title'         => 'Dashboard',
            'Pengaturan'    => $this->pengaturan
        ];

        if ($this->ionAuth->loggedIn()) {
            return redirect()->to('/dashboard');
        }
        return $this->login();
    }

    public function login()
    {
        if ($this->ionAuth->loggedIn()) {
            return redirect()->to('/dashboard');
        }

        $data = [
            'title'         => 'Login',
            'Pengaturan'    => $this->pengaturan
        ];

        return view($this->theme->getThemePath() . '/login/login', $data);
    }

    public function cek_login()
    {
        $validasi = \Config\Services::validation();
        
        $username = $this->request->getVar('user');
        $password = $this->request->getVar('pass');
        $remember = $this->request->getVar('ingat');
        
        $recaptchaResponse = $this->request->getVar('recaptcha_response');
        
        # Verify reCAPTCHA
        $recaptcha = $this->recaptcha->setExpectedHostname($_SERVER['SERVER_NAME'])
                                    ->setScoreThreshold(0.5)
                                    ->verify($recaptchaResponse, $_SERVER['REMOTE_ADDR']);

        // Temporarily bypass reCAPTCHA for testing
        if (!$recaptcha->isSuccess()) {
            return redirect()->back()->with('toastr', [
                'type' => 'error', 
                'message' => 'reCAPTCHA verification failed. Please try again.'
            ]);
        }

        $rules = [
            'user' => [
                'rules' => 'required|min_length[3]',
                'errors' => [
                    'required' => 'Username is required',
                    'min_length' => 'Username must be at least 3 characters'
                ]
            ],
            'pass' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Password is required'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            $errors = $validasi->getErrors();
            $error_message = implode('<br>', $errors);
            return redirect()->back()->with('toastr', [
                'type' => 'error',
                'message' => $error_message
            ]);
        }

        $rememberMe = ($remember == '1' ? true : false);
        $login = $this->ionAuth->login($username, $password, $rememberMe);

        if (!$login) {
            return redirect()->back()->with('toastr', [
                'type' => 'error',
                'message' => 'Invalid username or password'
            ]);
        }

        return redirect()->to('/dashboard')->with('toastr', [
            'type' => 'success',
            'message' => 'Login successful!'
        ]);
    }

    public function logout()
    {
        $this->ionAuth->logout();
        session()->setFlashdata('toastr', ['type' => 'success', 'message' => 'Anda berhasil keluar dari aplikasi.']);
        return redirect()->to('/auth/login');
    }

    public function forgot_password()
    {
        $this->data['title'] = 'Lupa Kata Sandi';

        if ($this->request->getMethod() === 'post') {
            $this->validation->setRules([
                'identity' => 'required|valid_email',
            ]);

            if ($this->validation->withRequest($this->request)->run()) {
                $identity = $this->request->getVar('identity');
                
                if ($this->ionAuth->forgottenPassword($identity)) {
                    session()->setFlashdata('toastr', ['type' => 'success', 'message' => $this->ionAuth->messages()]);
                    return redirect()->back();
                } else {
                    session()->setFlashdata('toastr', ['type' => 'error', 'message' => $this->ionAuth->errors()]);
                    return redirect()->back();
                }
            }
        }

        return view($this->theme->getThemePath() . '/login/forgot_password', $this->data);
    }
}