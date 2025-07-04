<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../app/Controlador/Controlador_Configuracion.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    $_SESSION['mensaje_error'] = "Debes iniciar sesión para acceder a esta página";
    header("Location: Login.php");
    exit();
}

$controlador = new Controlador_Configuracion();
$accion = $_GET['action'] ?? 'mostrar';

switch ($accion) {
    case 'guardar_notificaciones':
        $controlador->guardarNotificaciones();
        break;
    default:
        try {
            $datos_vista = $controlador->mostrarConfiguracion();
            
            if (!is_array($datos_vista)) {
                throw new Exception("No se pudieron cargar los datos de configuración");
            }
            
            extract($datos_vista);
            require_once __DIR__ . '/../app/Vista/chat/configuracion_notificaciones.php';
        } catch (Exception $e) {
            $_SESSION['mensaje_error'] = $e->getMessage();
            header("Location: index.php");
            exit();
        }
        break;
}
?>