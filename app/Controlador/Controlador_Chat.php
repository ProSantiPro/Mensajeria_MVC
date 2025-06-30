<?php
require_once(__DIR__ . '/../Modelo/Modelo_Usuario.php');
require_once(__DIR__ . '/../Modelo/Modelo_Chat.php');

class Controlador_Chat {
    private $modelo_usuario;
    private $modelo_chat;

    public function __construct() {
        $this->modelo_usuario = new Modelo_Usuario();
        $this->modelo_chat = new Modelo_Chat();
    }

    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['usuario'])) {
            header("Location: Login.php");
            exit();
        }

        $usuario_logeado = $_SESSION['usuario']['usuario_usuario'] ?? '';
        $correo_usuario_actual = '';
        $usuario_seleccionado = '';
        $mensajes_chat = [];
        $usuarios_totales = $this->modelo_usuario->Obtener_Usuarios();

        if (isset($_POST['Desconectar'])) {
            $this->modelo_usuario->Actualizar_Status($usuario_logeado, 'logout');
            session_destroy();
            header("Location: Login.php");
            exit();
        }

        if (isset($_GET['correo_usuario'])) {
            $correo_usuario_actual = $_GET['correo_usuario'];
            $usuario = $this->modelo_usuario->Obtener_Usuarios_email($correo_usuario_actual);
            
            if ($usuario) {
                $usuario_seleccionado = $usuario['usuario_usuario'];
                $_SESSION['usuario_seleccionado'] = $usuario_seleccionado;
                
                $this->modelo_chat->Mensajes_Vistos($usuario_seleccionado, $usuario_logeado);
                $mensajes_chat = $this->modelo_chat->Obtener_Mensajes_Chat($usuario_logeado, $usuario_seleccionado);
            }
        }

        if (isset($_POST['enviar']) && !empty($usuario_seleccionado)) {
            $contenido_mensaje = trim(htmlentities($_POST['msg_contenido']));
            
            // Debug: Verificar datos antes de insertar
            error_log("Intentando enviar mensaje de $usuario_logeado a $usuario_seleccionado: $contenido_mensaje");
            
            if ($usuario_logeado != $usuario_seleccionado && !empty($contenido_mensaje) && strlen($contenido_mensaje) <= 250) {
                if ($this->modelo_chat->Insertar_Mensaje($usuario_logeado, $usuario_seleccionado, $contenido_mensaje)) {
                    // Debug: Confirmar inserción exitosa
                    error_log("Mensaje insertado correctamente, redirigiendo...");
                    header("Location: ?correo_usuario=$correo_usuario_actual");
                    exit();
                } else {
                    error_log("Error al insertar mensaje");
                }
            } else {
                error_log("Validación fallida para enviar mensaje");
            }
        }


        $genero_usuario = $_SESSION['usuario']['genero'] ?? '';
        $saludo = ($genero_usuario == 'Femenino') ? 'Bienvenida' : 'Bienvenido';

        require_once(__DIR__ . '/../Vista/chat/index.php');
    }
}
?>
