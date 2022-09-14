<?php

/**
 * Front controller
 *
 * PHP version 7.0
 */


/**
 * Composer
 */
require dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Twig
 */
// Twig_Autoloader::register();

/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

/**
 * Sessions
 */
session_start();

/**
 * Routing
 */
$router = new Core\Router();

// Add the routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('login', ['controller' => 'Login', 'action' => 'new']);
$router->add('signup', ['controller' => 'Signup', 'action' => 'new']);
$router->add('logout', ['controller' => 'Login', 'action' => 'destroy']);
$router->add('logout/', ['controller' => 'Login', 'action' => 'destroy']);
$router->add('api/categories/', ['controller' => 'Category', 'action' => 'list']);
$router->add('api/categories/{sdate:\d+-\d+-\d+}and{edate:\d+-\d+-\d+}', ['controller' => 'Category', 'action' => 'list']);
$router->add('password/reset/{token:[\da-f]+}', ['controller' => 'Password', 'action' => 'reset']);
$router->add('signup/activate/{token:[\da-f]+}', ['controller' => 'Signup', 'action' => 'activate']);
$router->add('{controller}/{action}');

$router->dispatch($_SERVER['QUERY_STRING']);
