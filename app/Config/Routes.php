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
// PUBLIC (TANPA LOGIN)
// =======================
$routes->get('/', [AuthController::class, 'login']);
$routes->get('register', [AuthController::class, 'register']);

$routes->post('auth/processLogin', [AuthController::class, 'processLogin']);
$routes->post('auth/processRegister', [AuthController::class, 'processRegister']);
$routes->post('auth/checkEmail', [AuthController::class, 'checkEmail']);

// Logout tetap harus lewat auth (biar session aman)


// =======================
// PROTECTED (WAJIB LOGIN)
// =======================
$routes->group('', ['filter' => 'auth'], function ($routes) {

    // ================= DASHBOARD =================
    $routes->get('home', [HomeController::class, 'index']);
    $routes->get('dashboard', [HomeController::class, 'index']); // ← ini yang menyelamatkan dari 404

    // ================= AUTH =================
    $routes->post('logout', [AuthController::class, 'logout']);

    // ================= ASSETS =================
    $routes->group('assets', function ($routes) {
        $routes->get('/', [Assets::class, 'index']);
        $routes->get('create', [Assets::class, 'create']);
        $routes->post('/', [Assets::class, 'store']);
        $routes->get('(:num)', [Assets::class, 'show']);
        $routes->get('(:num)/edit', [Assets::class, 'edit']);
        $routes->post('(:num)/update', [Assets::class, 'update']);
        $routes->post('(:num)/delete', [Assets::class, 'delete']);
        $routes->get('monitoring', [Assets::class, 'monitoring']);
        $routes->post('monitoring-status', [Assets::class, 'monitoringStatus']);
    });

    // ================= API =================
    $routes->group('api', function ($routes) {
        $routes->get('assets', [Assets::class, 'apiList']);
    });

});
