<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', to: 'Auth::login');
$routes->get('/dashboard', 'Home::index', ['namespace' => 'App\Controllers']);

// Auth routes
$routes->group('auth', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('/', 'Auth::index');
    $routes->get('login', 'Auth::login');
    $routes->post('cek_login', 'Auth::cek_login');
    $routes->get('logout', 'Auth::logout');
    $routes->get('forgot-password', 'Auth::forgot_password');
    $routes->post('forgot-password', 'Auth::forgot_password');
});

// $routes->get('/dashboard', 'Dashboard::index', ['namespace' => 'App\Controllers', 'filter' => 'auth']);
