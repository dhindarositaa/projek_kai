<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\InputController;
use App\Controllers\BulkInputController;
use App\Controllers\BarangController;
use App\Controllers\ImportExcel;

/**
 * @var RouteCollection $routes
 */

// Home / Auth
$routes->get('/home', [HomeController::class, 'index']);
$routes->get('/', [AuthController::class, 'login']);
$routes->get('register', [AuthController::class, 'register']);
$routes->post('auth/processRegister', [AuthController::class, 'processRegister']);
$routes->post('auth/processLogin', [AuthController::class, 'processLogin']);
$routes->post('auth/checkEmail', [AuthController::class, 'checkEmail']);
$routes->get('auth/logout', [AuthController::class, 'logout']);

// pages
$routes->get('/input-manual', [InputController::class, 'index']);
$routes->get('/bulk-input', [BulkInputController::class, 'index']);
$routes->get('/barang', [BarangController::class, 'index']);

// import endpoints
$routes->post('import/process', [ImportExcel::class, 'process']);
$routes->get('import/logs', [ImportExcel::class, 'downloadLogs']);

// Assets CRUD
$routes->get('assets', 'Assets::index');
$routes->get('assets/create', 'Assets::create');
$routes->post('assets', 'Assets::store');
$routes->get('assets/(:num)', 'Assets::show/$1');
$routes->get('assets/(:num)/edit', 'Assets::edit/$1');
$routes->post('assets/(:num)/update', 'Assets::update/$1');
$routes->post('assets/(:num)/delete', 'Assets::delete/$1');


// optional API
$routes->get('api/assets', 'Assets::apiList');

// --------------------------------------------------------------------
