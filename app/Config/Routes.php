<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\HomeController;
use App\Controllers\AuthController;

/**
 * @var RouteCollection $routes
 */
// $routes->get('/', 'Home::index');

// app/Config/Routes.php

$routes->get('/', [HomeController::class, 'index']);
$routes->get('login', [AuthController::class, 'login']);
$routes->get('register', [AuthController::class, 'register']);
$routes->post('auth/processRegister', [AuthController::class, 'processRegister']);
$routes->post('auth/processLogin', [AuthController::class, 'processLogin']);
$routes->post('auth/checkEmail', [AuthController::class, 'checkEmail']);
$routes->get('auth/logout', [AuthController::class, 'logout']);
$routes->get('/input-manual', 'InputController::index');
$routes->get('/bulk-input', 'BulkInputController::index');
$routes->get('/barang', 'BarangController::index');