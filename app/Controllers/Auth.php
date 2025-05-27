<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
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
        
        $user = $this->request->getVar('user');
        $pass = $this->request->getVar('pass');
        $inga = $this->request->getVar('ingat');
        $recaptchaResponse = $this->request->getVar('recaptcha_response');
        
        $recaptcha = $this->recaptcha->setExpectedHostname($_SERVER['SERVER_NAME'])
                                     ->setScoreThreshold(config('Recaptcha')->score)
                                     ->verify($recaptchaResponse, $_SERVER['REMOTE_ADDR']);

        if (!$recaptcha->isSuccess()) {
            session()->setFlashdata('toastr', [
                'type' => 'error',
                'message' => 'reCAPTCHA verification failed'
            ]);
            return redirect()->back();
        }
        
        $aturan = [
            config('Security')->tokenName => 'required',
            'user' => [
                'rules'  => 'required|min_length[3]',
                'errors' => [
                    'required'   => 'ID Pengguna tidak boleh kosong',
                    'min_length' => 'Kolom {field} minimal 3 huruf',
                ]
            ],
            'pass' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Kata sandi tidak boleh kosong',
                ]
            ],
            'recaptcha_response' => 'required'
        ];
        
        $validasi->setRules($aturan);

        if (!$this->validate($aturan)) {
            $errors = $validasi->getErrors();
            $error_message = implode('<br>', $errors);
            session()->setFlashdata('toastr', ['type' => 'error', 'message' => $error_message]);
            return redirect()->back();
        }

        $cek = $this->ionAuth->usernameCheck($user);
        
        if (!$cek) {
            session()->setFlashdata('toastr', [
                'type' => 'error',
                'message' => 'ID Pengguna atau Kata Sandi salah!'
            ]);
            return redirect()->back();
        }

        $inget_ya = ($inga == '1' ? TRUE : FALSE);
        $login = $this->ionAuth->login($user, $pass, $inget_ya);

        if (!$login) {
            session()->setFlashdata('toastr', [
                'type' => 'error',
                'message' => 'ID Pengguna atau Kata Sandi salah!'
            ]);
            return redirect()->back();
        }

        session()->setFlashdata('toastr', [
            'type' => 'success',
            'message' => 'Login berhasil!'
        ]);
        return redirect()->to('/dashboard');
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