<?php
require_once(__DIR__ . '/../Modelo/Modelo_Usuario.php');

class Controlador_Configuracion {
    private $modelo_usuario;

    public function __construct() {
        $this->modelo_usuario = new Modelo_Usuario();
    }

    public function guardarNotificaciones() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['mensaje_error'] = "Método no permitido";
            header("Location: configuracion.php");
            exit();
        }

        if (!isset($_SESSION['usuario'])) {
            $_SESSION['mensaje_error'] = "Debes iniciar sesión para guardar configuraciones";
            header("Location: Login.php");
            exit();
        }

        $usuario_actual = $_SESSION['usuario']['usuario_usuario'];
        
        // Asegurarse de que recibimos un array de contactos
        $contactos_notificar = isset($_POST['contactos_notificar']) ? 
            (is_array($_POST['contactos_notificar']) ? $_POST['contactos_notificar'] : []) : [];
        
        $recibir_notificaciones = isset($_POST['recibir_notificaciones']) ? 1 : 0;

        // Validar que los contactos existan en la base de datos
        $usuarios_validos = $this->modelo_usuario->Obtener_Usuarios();
        $nombres_usuarios_validos = array_column($usuarios_validos, 'usuario_usuario');
        
        // Filtrar solo contactos válidos
        $contactos_notificar = array_filter($contactos_notificar, function($contacto) use ($nombres_usuarios_validos) {
            return in_array($contacto, $nombres_usuarios_validos);
        });

        try {
            $resultado = $this->modelo_usuario->Actualizar_Preferencias_Notificaciones(
                $usuario_actual,
                $recibir_notificaciones,
                array_values($contactos_notificar) // Asegurar índices numéricos
            );

            if ($resultado) {
                $_SESSION['mensaje_exito'] = "Configuración de notificaciones guardada correctamente";
                
                // Actualizar sesión con las nuevas preferencias
                $_SESSION['notificaciones_config'] = [
                    'notificaciones_email' => (bool)$recibir_notificaciones,
                    'contactos_notificar' => $contactos_notificar
                ];
            } else {
                $_SESSION['mensaje_error'] = "Error al guardar la configuración en la base de datos";
            }
        } catch (Exception $e) {
            error_log("Error al guardar notificaciones: " . $e->getMessage());
            $_SESSION['mensaje_error'] = "Error interno al guardar la configuración";
        }

        header("Location: configuracion.php");
        exit();
    }

        
   public function mostrarConfiguracion() {
        if (!isset($_SESSION['usuario'])) {
            header("Location: Login.php");
            exit();
        }

        $usuario_actual = $_SESSION['usuario']['usuario_usuario'];
        
        $preferencias = $this->modelo_usuario->Obtener_Preferencias_Notificaciones($usuario_actual);
        
        $todos_usuarios = $this->modelo_usuario->Obtener_Usuarios();
        $usuarios_disponibles = array_filter($todos_usuarios, function($usuario) use ($usuario_actual) {
            return $usuario['usuario_usuario'] !== $usuario_actual;
        });

        foreach ($usuarios_disponibles as &$usuario) {
            $datos_completos = $this->modelo_usuario->Obtener_Datos_Usuario($usuario['usuario_usuario']);
            $usuario['usuario_foto'] = $datos_completos['usuario_foto'] ?? null;
        }

        return [
            'preferencias' => $preferencias,
            'usuarios' => array_values($usuarios_disponibles), 
            'usuario_actual' => $usuario_actual,
            'ruta_base_fotos' => '/Mensajeria_MVC/CrudAdmin/app/views/fotos/'
        ];
    }
}
?>