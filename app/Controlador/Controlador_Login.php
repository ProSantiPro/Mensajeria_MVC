<?php
require_once __DIR__.'/../Modelo/Modelo_Usuario.php';

class Controlador_Login {
    private $modelo;
    private $conexion;

    public function __construct() {
        $this->modelo = new Modelo_Usuario();
        
        // Inicializar la conexión a la base de datos
        $database = new DataBase();
        $this->conexion = $database->Conectar_db();
    }

    public function procesarLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = $_POST['usuario'] ?? '';
            $password = $_POST['password'] ?? '';

            // Validar campos vacíos
            if (empty($usuario) || empty($password)) {
                header("Location: ../public/Login.php?error=1");
                exit();
            }

            // Obtener usuario de la base de datos
            $stmt = $this->conexion->prepare("SELECT usuario_id, usuario_usuario, usuario_clave FROM usuario WHERE usuario_usuario = ? LIMIT 1");
            $stmt->bind_param("s", $usuario);
            
            if (!$stmt->execute()) {
                error_log("Error al ejecutar la consulta: " . $stmt->error);
                header("Location: ../public/Login.php?error=2");
                exit();
            }

            $resultado = $stmt->get_result();
            
            // Verificar si el usuario existe
            if ($resultado->num_rows === 0) {
                header("Location: ../public/Login.php?error=1");
                exit();
            }

            $user = $resultado->fetch_assoc();
            
            // Verificar contraseña (tanto hasheada como en texto plano para migración)
            $login_valido = false;
            
            // Primero intentar con password_verify
            if (password_verify($password, $user['usuario_clave'])) {
                $login_valido = true;
            } 
            // Si falla, verificar si es contraseña en texto plano (solo para migración)
            elseif ($password === $user['usuario_clave']) {
                $login_valido = true;
                // Hashear la contraseña y actualizarla en la base de datos
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $updateStmt = $this->conexion->prepare("UPDATE usuario SET usuario_clave = ? WHERE usuario_usuario = ?");
                $updateStmt->bind_param("ss", $hashedPassword, $usuario);
                $updateStmt->execute();
            }

            if ($login_valido) {
                // Iniciar sesión
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                // Obtener datos completos del usuario
                $datosUsuario = $this->modelo->Obtener_Datos_Usuario($usuario);
                
                // Establecer datos de sesión
                $_SESSION['usuario'] = [
                    'usuario_id' => $datosUsuario['usuario_id'],
                    'usuario_usuario' => $datosUsuario['usuario_usuario'],
                    'usuario_email' => $datosUsuario['usuario_email'],
                    'usuario_foto' => $datosUsuario['usuario_foto'],
                    'genero' => $datosUsuario['genero'] ?? '',
                    'login_status' => 'Online'
                ];
                
                // Actualizar estado de conexión
                $this->modelo->Actualizar_Status($usuario, 'Online');
                
                // Redirigir al index
                header("Location: ../../public/index.php");
                exit();
            } else {
                header("Location: ../../public/Login.php?error=1");
                exit();
            }
        }
    }
}

// Procesar el login cuando se instancia el controlador
$controlador = new Controlador_Login();
$controlador->procesarLogin();