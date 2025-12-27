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

// ======================================================
// AUTH & HOME
// ======================================================
$routes->get('/', [AuthController::class, 'login']);
$routes->get('home', [HomeController::class, 'index']);

$routes->get('register', [AuthController::class, 'register']);
$routes->post('auth/processRegister', [AuthController::class, 'processRegister']);
$routes->post('auth/processLogin', [AuthController::class, 'processLogin']);
$routes->post('auth/checkEmail', [AuthController::class, 'checkEmail']);
$routes->post('logout', [AuthController::class, 'logout']);


// ======================================================
// INPUT DATA
// ======================================================
$routes->group('input', function ($routes) {
    $routes->get('/', [InputController::class, 'index']);
    $routes->post('store', [InputController::class, 'store']);
});

$routes->get('bulk-input', [BulkInputController::class, 'index']);


// ======================================================
// BARANG (LEGACY / LIST)
// ======================================================
$routes->get('barang', [BarangController::class, 'index']);


// ======================================================
// IMPORT EXCEL
// ======================================================
$routes->group('import', function ($routes) {
    $routes->post('process', [ImportExcel::class, 'process']);
    $routes->get('logs', [ImportExcel::class, 'downloadLogs']);
});


// ======================================================
// ASSETS (CRUD + MONITORING)
// ======================================================
$routes->group('assets', function ($routes) {

    // CRUD
    $routes->get('/', [Assets::class, 'index']);
    $routes->get('create', [Assets::class, 'create']);
    $routes->post('/', [Assets::class, 'store']);

    $routes->get('(:num)', [Assets::class, 'show']);
    $routes->get('(:num)/edit', [Assets::class, 'edit']);
    $routes->post('(:num)/update', [Assets::class, 'update']);
    $routes->post('(:num)/delete', [Assets::class, 'delete']);

    // Monitoring & bulk status
    $routes->get('monitoring', [Assets::class, 'monitoring']);
    $routes->post('monitoring-status', [Assets::class, 'monitoringStatus']);
});


// ======================================================
// API (OPTIONAL)
// ======================================================
$routes->group('api', function ($routes) {
    $routes->get('assets', [Assets::class, 'apiList']);
});


// ======================================================
// TEST / DEV
// ======================================================
$routes->get('test', 'Test::index');
