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

        $this->modelo_chat->Mensajes_Vistos($usuario_seleccionado, $usuario_logeado);

        
        if (isset($_POST['enviar']) && !empty($usuario_seleccionado)) {
            $contenido_mensaje = trim(htmlentities($_POST['msg_contenido']));
            
            if ($usuario_logeado != $usuario_seleccionado && !empty($contenido_mensaje) && strlen($contenido_mensaje) <= 250) {
                // Buffer para capturar posibles salidas no deseadas
                ob_start();
                
                try {
                    // 1. Primero inserta el mensaje en la base de datos
                    $mensajeInsertado = $this->modelo_chat->Insertar_Mensaje(
                        $usuario_logeado, 
                        $usuario_seleccionado, 
                        $contenido_mensaje
                    );
                    
                    if ($mensajeInsertado) {
                        // 2. Elimina el borrador SI existe
                        if (isset($_SESSION['borradores'][$usuario_seleccionado])) {
                            unset($_SESSION['borradores'][$usuario_seleccionado]);
                        }
                        
                        // 3. Limpia el buffer ANTES de redirigir
                        ob_end_clean();
                        
                        // 4. Redirige inmediatamente (el correo se envía en segundo plano)
                        header("Location: ?correo_usuario=$correo_usuario_actual");
                        exit();
                    }
                    
                } catch (Exception $e) {
                    error_log("Error al enviar mensaje: " . $e->getMessage());
                    $_SESSION['error_chat'] = "Error al enviar el mensaje";
                }
                
                // Si llegamos aquí, hubo un error
                ob_end_flush();
            }
        }

        $genero_usuario = $_SESSION['usuario']['genero'] ?? '';
        $saludo = ($genero_usuario == 'Femenino') ? 'Bienvenida' : 'Bienvenido';

        require_once(__DIR__ . '/../Vista/chat/index.php');
    }

    
}
?>
