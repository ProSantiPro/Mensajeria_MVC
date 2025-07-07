<?php
session_start();
require_once __DIR__ . '/../app/Modelo/Modelo_Chat.php';
require_once __DIR__ . '/../app/Modelo/Modelo_Archivos.php';

if (!isset($_SESSION['usuario']) || empty($_GET['usuario_seleccionado'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Sesión no válida o usuario no seleccionado']);
    exit();
}


if (!isset($_GET['usuario_seleccionado'])) {
    echo json_encode(['error' => 'Usuario no seleccionado']);
    exit();
}

$usuario_logeado = $_SESSION['usuario']['usuario_usuario'];
$usuario_seleccionado = $_GET['usuario_seleccionado'];

$modelo_chat = new Modelo_Chat();
$modelo_archivos = new Modelo_Archivos();

$mensajes = $modelo_chat->Obtener_Mensajes_Chat($usuario_logeado, $usuario_seleccionado);

$ultimo_msg_id = $_SESSION['ultimo_msg_id'] ?? 0;

// Verificar si hay mensajes nuevos
$nuevosMensajes = false;
foreach ($mensajes as $msg) {
    if ($msg['msg_id'] > $ultimo_msg_id && $msg['sender_usuario'] == $usuario_seleccionado) {
        $nuevosMensajes = true;
        $_SESSION['ultimo_msg_id'] = $msg['msg_id']; // Actualizar el último ID
        break;
    }
}

echo json_encode(['nuevosMensajes' => $nuevosMensajes]);

$ultimo_archivo_id = $_SESSION['ultimo_archivo_id_'.$usuario_seleccionado] ?? 0;

$archivos = $modelo_archivos->Obtener_Archivos_Compartidos($usuario_logeado, $usuario_seleccionado);
$nuevosArchivos = false;

foreach ($archivos as $archivo) {
    if ($archivo['id'] > $ultimo_archivo_id && $archivo['sender_usuario'] == $usuario_seleccionado) {
        $nuevosArchivos = true;
        $_SESSION['ultimo_archivo_id_'.$usuario_seleccionado] = $archivo['id'];
        break;
    }
}

?>