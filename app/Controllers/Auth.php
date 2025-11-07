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
use App\Models\GudangModel;


class Auth extends BaseController
{
    protected $recaptcha;
    protected $gudangModel;
    
    public function __construct()
    {
        $recaptchaModel = new \App\Models\ReCaptchaModel();
        $this->recaptcha = new ReCaptcha($recaptchaModel->getSecretKey());

        $this->gudangModel         = new GudangModel();
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

    public function login_kasir()
    {
        if ($this->ionAuth->loggedIn()) {
            return redirect()->to('/dashboard');
        }

        try {
            // Load outlets data for dropdown
            $gudangModel = new \App\Models\GudangModel();
            $outlets = $gudangModel->getOutlets(); // Uses: status=1, status_otl=1, status_hps=0

            $data = [
                'title'         => 'Login Kasir',
                'Pengaturan'    => $this->pengaturan,
                'outlets'       => $outlets
            ];

            return view($this->theme->getThemePath() . '/login/login_kasir', $data);
        } catch (\Exception $e) {
            // Log the error
            log_message('error', 'Error in login_kasir: ' . $e->getMessage());
            
            // Return error view or fallback
            $data = [
                'title'         => 'Login Kasir - Error',
                'Pengaturan'    => $this->pengaturan ?? null,
                'outlets'       => [],
                'error'         => $e->getMessage()
            ];
            
            return view($this->theme->getThemePath() . '/login/login_kasir', $data);
        }
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

        // Check for active shift after successful login
        $user = $this->ionAuth->user()->row();
        $shiftModel = new \App\Models\ShiftModel();
        
        // Check if user has an active shift that blocks login
        if ($shiftModel->hasActiveShiftBlockingLogin($user->id)) {
            $activeShift = $shiftModel->getUserActiveShift($user->id);
            
            // Store shift info in session for continue prompt
            session()->set('pending_shift_id', $activeShift['id']);
            session()->set('pending_shift_code', $activeShift['shift_code']);
            
            return redirect()->to('/transaksi/shift/continue')->with('toastr', [
                'type' => 'warning',
                'message' => 'Anda memiliki shift aktif. Silakan lanjutkan atau tutup shift terlebih dahulu.'
            ]);
        }

        return redirect()->to('/dashboard')->with('toastr', [
            'type' => 'success',
            'message' => 'Login successful!'
        ]);
    }

    public function cek_login_kasir()
    {
        $validasi = \Config\Services::validation();
        
        $username = $this->request->getVar('user');
        $password = $this->request->getVar('pass');
        $remember = $this->request->getVar('ingat');
        $outlet = $this->request->getVar('outlet');
        
        $recaptchaResponse = $this->request->getVar('recaptcha_response');
        
        # Verify reCAPTCHA
        $recaptcha = $this->recaptcha->setExpectedHostname($_SERVER['SERVER_NAME'])
                                    ->setScoreThreshold(0.5)
                                    ->verify($recaptchaResponse, $_SERVER['REMOTE_ADDR']);

        if (!$recaptcha->isSuccess()) {
            return redirect()->to(base_url('auth/login-kasir'))->with('toastr', [
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
            ],
            'outlet' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Outlet is required'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            $errors = $validasi->getErrors();
            $error_message = implode('<br>', $errors);
            return redirect()->to(base_url('auth/login-kasir'))->with('toastr', [
                'type' => 'error', 
                'message' => $error_message
            ]);
        }

        $rememberMe = ($remember == '1' ? true : false);
        $login = $this->ionAuth->login($username, $password, $rememberMe);

        if (!$login) {
            return redirect()->to(base_url('auth/login-kasir'))->with('toastr', [
                'type' => 'error', 
                'message' => 'Invalid username or password'
            ]);
        }

        // Check for active shift after successful login
        $user = $this->ionAuth->user()->row();
        $shiftModel = new \App\Models\ShiftModel();
        
        // Check if user has an active shift that blocks login
        if ($shiftModel->hasActiveShiftBlockingLogin($user->id)) {
            $activeShift = $shiftModel->getUserActiveShift($user->id);
            
            // Store shift info in session for continue prompt
            session()->set('pending_shift_id', $activeShift['id']);
            session()->set('pending_shift_code', $activeShift['shift_code']);
            session()->set('pending_outlet_id', $outlet);
            
            // Redirect to continue shift page
            return redirect()->to('/transaksi/shift/continue/' . $activeShift['id'])->with('toastr', [
                'type' => 'warning',
                'message' => 'Anda memiliki shift aktif. Silakan lanjutkan atau tutup shift terlebih dahulu.'
            ]);
        }

        $outlet_name = $this->gudangModel->find($outlet)->nama;

        // Store outlet info in session for kasir
        session()->set('kasir_outlet', $outlet);
        session()->set('kasir_outlet_name', $outlet_name);

        return redirect()->to('/dashboard')->with('toastr', [
            'type' => 'success',
            'message' => 'Login kasir successful!'
        ]);
    }

    /**
     * Show continue shift prompt page
     */
    public function continue_shift()
    {
        if (!$this->ionAuth->loggedIn()) {
            return redirect()->to('/auth/login');
        }

        $pendingShiftId = session()->get('pending_shift_id');
        if (!$pendingShiftId) {
            return redirect()->to('/dashboard');
        }

        $shiftModel = new \App\Models\ShiftModel();
        $shift = $shiftModel->getShiftWithDetails($pendingShiftId);

        if (!$shift || $shift['status'] !== 'open') {
            session()->remove(['pending_shift_id', 'pending_shift_code', 'pending_outlet_id']);
            return redirect()->to('/dashboard');
        }

        $data = [
            'title' => 'Lanjutkan Shift',
            'Pengaturan' => $this->pengaturan,
            'shift' => $shift
        ];

        return view($this->theme->getThemePath() . '/auth/continue_shift', $data);
    }

    public function logout()
    {
        $this->ionAuth->logout();
        // Destroy all session data
        session()->destroy();
        // Set flashdata after destroying session (using tempdata as workaround)
        session()->setTempdata('toastr', ['type' => 'success', 'message' => 'Anda berhasil keluar dari aplikasi.'], 5);
        return redirect()->to('/auth/login');
    }

    public function logout_kasir()
    {
        $this->ionAuth->logout();
        session()->setFlashdata('toastr', ['type' => 'success', 'message' => 'Anda berhasil keluar dari aplikasi.']);
        // Remove kasir outlet session if exists
        session()->remove('kasir_outlet');
        return redirect()->to('/auth/login-kasir');
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