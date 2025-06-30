<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../app/Controlador/Controlador_Archivos.php';

try{
    $controlador = new Controlador_Archivos();
    $accion = $_GET['action'] ?? 'index';
    $id = $_GET['id'] ?? null;

    switch ($accion){
        case 'subir':
            $controlador->subir();
            break;
        case 'eliminar':
            $controlador->eliminar($id);
            break;
        case 'descargar':
            $controlador->descargar($id);
            break;
        default:
            $controlador->index();
            break;
    }
} catch (Exception $e) {
    error_log("Error en archivos.php: ". $e->getMessage());
    header("Location: archivos.php?error=4");
    exit();
}

?>