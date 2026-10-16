<?php
define('ROOT', dirname(__DIR__));
define('APP', ROOT . '/app');

$config = require ROOT . '/config/app.php';
date_default_timezone_set($config['timezone']);

ini_set('display_errors', $config['debug'] ? '1' : '0');
error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('error_log', ROOT . '/storage/logs/error.log');

session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

spl_autoload_register(function ($class) {
    foreach (['core', 'controllers', 'models', 'middleware'] as $dir) {
        $file = APP . "/$dir/$class.php";
        if (is_file($file)) { require $file; return; }
    }
});

require APP . '/helpers/functions.php';
require APP . '/helpers/validation.php';
require APP . '/helpers/response.php';
require APP . '/helpers/mailer.php';

// Tự đăng nhập lại nếu người dùng đã chọn "Ghi nhớ đăng nhập"
User::restore();

$router = new Router();
require ROOT . '/routes/web.php';
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
