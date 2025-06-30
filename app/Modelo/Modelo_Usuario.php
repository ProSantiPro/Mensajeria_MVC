<?php
require_once(__DIR__ . '/../../config/Database.php');

class Modelo_Usuario {
    private $conexion;

    public function __construct() {
        $database = new DataBase();
        $this->conexion = $database->Conectar_db();
    }

    public function Validar_Usuario($usuario, $password) {
        $stmt = $this->conexion->prepare("SELECT usuario_id, usuario_usuario, usuario_clave FROM usuario WHERE usuario_usuario = ? LIMIT 1");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows === 0) return false;
        
        $user = $resultado->fetch_assoc();
        return password_verify($password, $user['usuario_clave']);
    }

    public function Obtener_Usuarios_genero($usuario) {
        $stmt = $this->conexion->prepare("SELECT genero FROM usuario WHERE usuario_usuario = ? LIMIT 1");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc()['genero'] ?? null;
    }

    public function Actualizar_Status($usuario, $status) {
        $stmt = $this->conexion->prepare("UPDATE usuario SET login_status = ? WHERE usuario_usuario = ?");
        $stmt->bind_param("ss", $status, $usuario);
        return $stmt->execute();
    }

    public function Obtener_Usuarios() {
        $result = $this->conexion->query("
            SELECT usuario_id, usuario_usuario, usuario_email, genero, login_status 
            FROM usuario 
            WHERE usuario_usuario != '' AND usuario_email != ''
            ORDER BY login_status DESC, usuario_usuario ASC
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function Obtener_Usuarios_email($email) {
        $stmt = $this->conexion->prepare("SELECT usuario_id, usuario_usuario, usuario_email, genero FROM usuario WHERE usuario_email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }
    
    public function Obtener_Datos_Usuario($usuario) {
        $stmt = $this->conexion->prepare("SELECT usuario_id, usuario_usuario, usuario_email, genero FROM usuario WHERE usuario_usuario = ? LIMIT 1");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }
}
?>