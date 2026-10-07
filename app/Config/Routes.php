<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/login', 'Home::login');
$routes->post('/login', 'Home::login');
$routes->get('/dashboard', 'Home::dashboard');
$routes->post('/logout', 'Home::logout');
$routes->get('/account/(:num)', 'Home::viewAccount/$1');
$routes->get('/account/new', 'Home::newAccount');
$routes->post('/account/create', 'Home::createAccount');
$routes->get('/account/(:num)/edit', 'Home::editAccount/$1');
$routes->post('/account/(:num)/update', 'Home::updateAccount/$1');
$routes->post('/account/(:num)/delete', 'Home::deleteAccount/$1');
$routes->get('/about', 'About::index');
$routes->get('/services', 'Services::index');
$routes->match(['get', 'post'], '/contact', 'Contact::index');
$routes->get('/register', 'Register::index');
$routes->post('/register', 'Register::create');
