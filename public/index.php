<?php

declare(strict_types = 1);

use App\App;
use App\Config;
use App\Controllers\HomeController;
use App\Controllers\TransactionController;
use App\Router;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

define('STORAGE_PATH', __DIR__ . '/../storage');
define('VIEW_PATH', __DIR__ . '/../views');
define('APP_PATH',  __DIR__ . '/../app');

require APP_PATH . '/helpers.php';

$router = new Router();

$router
    ->get('/', [HomeController::class, 'index'])
    ->get('/transactions', [TransactionController::class, 'index'])
    ->get('/transactions/upload',[TransactionController::class, 'upload'])
    ->post('/transactions/upload', [TransactionController::class, 'store']);

(new App(
    $router,
    ['uri' => $_SERVER['REQUEST_URI'], 'method' => $_SERVER['REQUEST_METHOD']],
    new Config($_ENV)
))->run();
