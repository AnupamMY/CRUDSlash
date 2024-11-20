<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('/saveUser', 'Home::saveUser');
$routes->get('/getSingleUser/(:num)', 'Home::getSingleUser/$1');
$routes->post('/updateUser', 'Home::updateUser');
$routes->post('/deleteUser', 'Home::deleteUser');
$routes->post('/deleteAllUser', 'Home::deleteAllUser');
$routes->post('/create', 'Home::deleteAllUser');