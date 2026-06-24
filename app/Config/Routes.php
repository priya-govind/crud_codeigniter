<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->group('', ['filter' => 'guest'], function($routes) {
$routes->get('/','AuthController::index');
$routes->get('/register','AuthController::register');
$routes->post('/store','AuthController::store');
$routes->get('/login','AuthController::login');
$routes->post('/attemptLogin','AuthController::attemptLogin');
});


$routes->group('', ['filter' => 'auth'], function($routes) {
        $routes->get('/dashboard', function(){
            return view('dashboard');
        });
        $routes->get('/users', 'UserController::index');
        $routes->get('/users/getUsers', 'UserController::getUsers');
        $routes->get('/users/show/(:num)', 'UserController::show/$1');
        $routes->get('/users/edit/(:num)', 'UserController::edit/$1');
        $routes->post('/users/update/(:num)', 'UserController::update/$1');
        $routes->get('/users/delete/(:num)', 'UserController::delete/$1');
        $routes->get('/users/getUsers', 'UserController::getUsers');
        $routes->get('/logout','AuthController::logout');
});

