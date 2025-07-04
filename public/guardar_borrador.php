<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("HTTP/1.1 403 Forbidden");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $destinatario = $_POST['destinatario'] ?? '';
    $mensaje = $_POST['mensaje'] ?? '';
    
    if (!empty($destinatario) && !empty($mensaje)) {
        if (!isset($_SESSION['borradores'])) {
            $_SESSION['borradores'] = [];
        }
        
        $_SESSION['borradores'][$destinatario] = $mensaje;
        echo "OK";
    } else {
        header("HTTP/1.1 400 Bad Request");
    }
} else {
    header("HTTP/1.1 405 Method Not Allowed");
}
?>