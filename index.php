<?php

// контроль ошибок
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Подключение сессии
session_start();

//Подключение файлов системы
define('ROOT', dirname(__FILE__));
require_once(ROOT . '/components/Autoload.php');

//Подключение Router
$router = new Router();
$router->run();

//echo 'Vit php работает!!!'.ROOT;
