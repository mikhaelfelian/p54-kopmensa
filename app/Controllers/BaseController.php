<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use CodeIgniter\I18n\Time;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
/**
 * BaseController
 * 
 * Base controller class that provides common functionality and properties
 * for all controllers in the application
 * 
 * @author    Mikhael Felian Waskito <mikhaelfelian@gmail.com>
 * @date      2025-01-12
 */

abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['tanggalan', 'general', 'theme', 'angka'];

    /**
     * Data array for views
     */
    protected $data = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    protected $theDate;
    protected $theTime;
    protected $pengaturan;
    protected $theme;

    public function __construct()
    {
        $this->theDate = new Time();
        $this->theTime = Time::now('Asia/Jakarta');
        $this->theme = new \App\Models\PengaturanThemeModel();
    }

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Set timezone first
        if (function_exists('date_default_timezone_set')) {
            date_default_timezone_set('Asia/Jakarta');
        }

        // Load helpers
        helper(['form', 'url', 'html', 'theme']);
        
        // Initialize validation service
        $this->validation            = \Config\Services::validation();

        // Initialize core services
        $this->db                   = \Config\Database::connect();
        $this->ionAuth              = new \IonAuth\Libraries\IonAuth();
        $this->session              = \Config\Services::session();

        // Initialize settings and theme models
        $pengaturanModel            = new \App\Models\PengaturanModel();
        $this->pengaturan           = $pengaturanModel->asObject()->where('id', 1)->first();
        $this->theme                = new \App\Models\PengaturanThemeModel();

        // Set default view data
        $this->data['user']         = $this->ionAuth->user()->row();
        $this->data['Pengaturan']   = $this->pengaturan;

        // Check if this is a protected page
        $router = service('router');
        $protected = ['Dashboard'];
        if (in_array($router->controllerName(), $protected) && !$this->ionAuth->loggedIn()) {
            return redirect()->to('/auth/login');
        }
    }

    /**
     * Override view() to automatically include default data
     */
    protected function view(string $name, array $data = [], array $options = []): string
    {
        // Merge default data with passed data
        $data = array_merge($this->data, $data);
        
        return view($name, $data, $options);
    }
}
