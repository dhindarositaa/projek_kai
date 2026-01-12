<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\InputController;
use App\Controllers\BulkInputController;
use App\Controllers\BarangController;
use App\Controllers\ImportExcel;
use App\Controllers\Assets;

/**
 * @var RouteCollection $routes
 */

// =======================
// PUBLIC
// =======================
$routes->get('/', [AuthController::class, 'login']);
$routes->get('register', [AuthController::class, 'register']);

$routes->post('auth/processRegister', [AuthController::class, 'processRegister']);
$routes->post('auth/processLogin', [AuthController::class, 'processLogin']);
$routes->post('auth/checkEmail', [AuthController::class, 'checkEmail']);
$routes->post('logout', [AuthController::class, 'logout']);


// =======================
// PROTECTED (LOGIN)
// =======================
$routes->group('', ['filter' => 'auth'], function ($routes) {

    // home / dashboard
    $routes->get('home', [HomeController::class, 'index']);
    $routes->get('dashboard', [HomeController::class, 'index']);

    // pages
    $routes->get('input', [InputController::class, 'index']);
    $routes->post('input/store', [InputController::class, 'store']);
    $routes->get('bulk-input', [BulkInputController::class, 'index']);
    $routes->get('barang', [BarangController::class, 'index']);

    // assets
    $routes->get('assets', 'Assets::index');
    $routes->get('assets/create', 'Assets::create');
    $routes->post('assets', 'Assets::store');
    $routes->get('assets/(:num)', 'Assets::show/$1');
    $routes->get('assets/(:num)/edit', 'Assets::edit/$1');
    $routes->post('assets/(:num)/update', 'Assets::update/$1');
    $routes->post('assets/(:num)/delete', 'Assets::delete/$1');
    $routes->get('assets/monitoring', 'Assets::monitoring');
    $routes->post('assets/monitoring-status', 'Assets::monitoringStatus');

    // API
    $routes->get('api/assets', 'Assets::apiList');
});
