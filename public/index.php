<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);

define('APP_ROOT', dirname(__DIR__));
define('CONTROLLER_PATH', APP_ROOT . '/app/Controlador');

require_once APP_ROOT . '/config/session.php';

if(!isset($_SESSION['usuario'])) {
    header("Location: Login.php");
    exit();
}

require_once CONTROLLER_PATH . '/Controlador_Chat.php';

if(!class_exists('Controlador_Chat')) {
    die("Error: La clase Controlador_Chat no está definida");
}

try {
    $controller = new Controlador_Chat();
    $controller->index();
} catch (Exception $e) {
    die("Error inicializando controlador: ". $e->getMessage());
}