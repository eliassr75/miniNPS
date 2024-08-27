<?php

namespace App\Controllers;
require_once '../config/cors.php';
require '../vendor/autoload.php';
require '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === "GET"){
    header("Content-type: text/html; charset=utf-8");

    if(isset($_GET['redirect']) && !empty($_GET['url'])){
        $_SESSION["redirect"] = $_GET['url'];
    }

}else{
    header("Content-type: application/json; charset=utf-8");
}

use App\Middleware;
use App\Router;

$middleware = new Middleware();
$router = new Router();

//MAIN ROUTES
$router->addRoute('GET', '', false,  "login", false,LoginController::class, 'redirectToLogin');
$router->addRoute('GET', '/', false,  "login", false,LoginController::class, 'redirectToLogin');
$router->addRoute('GET', '/error/{codeError}/', false,  "error", false,ErrorController::class, 'errorPage');

// LOGIN ROUTES
$router->addRoute('GET', '/logout/', false,  "logout", false,LoginController::class, 'logout');

$router->addRoute('GET', '/login/', false,  "login", false,LoginController::class, 'login');
$router->addRoute('POST', '/login/', false,  "login", false,LoginController::class, 'authenticate');

$router->addRoute('GET', '/new-account/', false,  "login", false,LoginController::class, 'newAccount');
$router->addRoute('POST', '/new-account/', false,  "global", false,LoginController::class, 'createAccount');
$router->addRoute('GET', '/new-account/continue/{token}/', false, "global", false,LoginController::class, 'linkManager');

$router->addRoute('GET', '/forget-password/', false,  "login", false,LoginController::class, 'forgetPassword');
$router->addRoute('POST', '/forget-password/', false,  "login", false,LoginController::class, 'forgetPassword');

$router->addRoute('GET', '/forget-password/{token}/', false,  "login", false,LoginController::class, 'forgetPassword');
$router->addRoute('PUT', '/forget-password/{token}/', false,  "login", false,LoginController::class, 'resetPassword');

$router->addRoute('GET', '/missing-data/{userId}/', true,  "system", [1, 2, 3, 4],MissingDataController::class, 'index');
$router->addRoute('PUT', '/missing-data/{userId}/', true, "system", [1, 2, 3, 4],MissingDataController::class, 'missingData');

// SYSTEM ROUTES
$router->addRoute('GET', '/dashboard/', true, "system", [1, 2, 3, 4],DashboardController::class, 'index');

$router->addRoute('GET', '/users/', true, "system", [1],UserController::class, 'index');
$router->addRoute('PUT', '/users/{userId}/', true, "system", [1],UserController::class, 'updateUser');
$router->addRoute('PUT', '/users/change/{userId}/', true, "system", [1],UserController::class, 'changeUser');
$router->addRoute('GET', '/users/json/', true, "system", [1],UserController::class, 'json');
$router->addRoute('GET', '/users/json/{userId}/', true, "system", [1],UserController::class, 'json');

$router->addRoute('GET', '/clients/', true, "system", [1, 2, 3],ClientController::class, 'index');
$router->addRoute('GET', '/clients/json/', true, "system", [1, 2, 3],ClientController::class, 'json');

$router->addRoute('GET', '/client/new/', true, "system", [1, 2, 3],ClientController::class, 'getClient');
$router->addRoute('POST', '/client/new/', true, "system", [1, 2, 3],ClientController::class, 'newClient');
$router->addRoute('GET', '/client/{clientId}/', true, "system", [1, 2, 3],ClientController::class, 'getClient');
$router->addRoute('GET', '/client/json/{clientId}/', true, "system", [1, 2, 3],ClientController::class, 'json');
$router->addRoute('PUT', '/client/{clientId}/', true, "system", [1, 2, 3],ClientController::class, 'updateClient');
$router->addRoute('PUT', '/client/change/{clientId}/', true, "system", [1, 2, 3],ClientController::class, 'changeClient');

$router->addRoute('GET', '/settings/', true, "system", [1], SettingsController::class, 'index');
$router->addRoute('GET', '/settings/category/', true, "system", [1], SettingsController::class, 'category');
$router->addRoute('GET', '/settings/category/{categoryId}/', true, "system", [1], SettingsController::class, 'category');
$router->addRoute('GET', '/settings/category/{categoryId}/{json}', true, "system", [1], SettingsController::class, 'category');
$router->addRoute('GET', '/settings/glass_type/', true, "system", [1], SettingsController::class, 'glass_type');
$router->addRoute('GET', '/settings/glass_thickness/', true, "system", [1], SettingsController::class, 'glass_thickness');
$router->addRoute('GET', '/settings/glass_colors/', true, "system", [1], SettingsController::class, 'glass_colors');
$router->addRoute('GET', '/settings/glass_finish/', true, "system", [1], SettingsController::class, 'glass_finish');
$router->addRoute('GET', '/settings/glass_clearances/', true, "system", [1], SettingsController::class, 'glass_clearances');
$router->addRoute('GET', '/settings/print_templates/', true, "system", [1], SettingsController::class, 'print_templates');
$router->addRoute('GET', '/settings/emails/', true, "system", [1], SettingsController::class, 'emails');
$router->addRoute('POST', '/settings/{routeName}/', true, "system", [1], SettingsController::class, 'create');
$router->addRoute('PUT', '/settings/{routeName}/{Id}/', true, "system", [1], SettingsController::class, 'update');

$router->addRoute('GET', '/products/', true, "system", [1, 2, 3],ProductController::class, 'index');
$router->addRoute('GET', '/product/{productId}/', true, "system", [1, 2, 3],ProductController::class, 'getProduct');
$router->addRoute('PUT', '/product/{productId}/', true, "system", [1, 2, 3],ProductController::class, 'updateProduct');
$router->addRoute('PUT', '/product/change/{productId}/', true, "system", [1, 2, 3],ProductController::class, 'changeProduct');

$router->addRoute('GET', '/orders/', true, "system", [1, 2, 3, 4],OrdersController::class, 'index');
$router->addRoute('GET', '/order/new/', true, "system", [1, 2, 3],OrdersController::class, 'getOrder');
$router->addRoute('POST', '/order/new/', true, "system", [1, 2, 3],OrdersController::class, 'newOrder');
$router->addRoute('GET', '/order/{orderId}/', true, "system", [1, 2, 3, 4],OrdersController::class, 'getOrder');
$router->addRoute('PUT', '/order/{orderId}/', true, "system", [1, 2, 3, 4],OrdersController::class, 'updateOrder');

$router->addRoute('POST', '/uploads/addImage/', true, "system", [1, 2, 3], UploadsController::class, 'addImage');
$router->addRoute('GET', '/print/{routeName}/{Id}/', true, "system", [1, 2, 3, 4], SettingsController::class, 'print');
$router->addRoute('GET', '/print/{routeName}/{Id}/{printId}/', true, "system", [1, 2, 3, 4], SettingsController::class, 'print');

$middleware->autoRedirect();
$router->handleRequest();
