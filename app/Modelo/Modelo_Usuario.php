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
        
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    public function Obtener_Usuarios_email($email) {
        $stmt = $this->conexion->prepare("SELECT usuario_id, usuario_usuario, usuario_email, genero FROM usuario WHERE usuario_email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }
    
    public function Obtener_Datos_Usuario($usuario) {
        $stmt = $this->conexion->prepare("SELECT usuario_id, usuario_usuario, usuario_email, usuario_foto, genero FROM usuario WHERE usuario_usuario = ? LIMIT 1");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $datos = $resultado->fetch_assoc();

        if(empty($datos['usuario_foto'])){
            $foto = $this->buscarFotoUsuario($usuario);
            if($foto){
                $datos['usuario_foto'] = $foto;
            }
        }
        return $datos;
    }

    public function Obtener_Preferencias_Notificaciones($usuario) {
        $stmt = $this->conexion->prepare("SELECT notificaciones_email, contactos_notificar FROM usuario WHERE usuario_usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows === 0) {
            return [
                'notificaciones_email' => true,
                'contactos_notificar' => []
            ];
        }
        
        $datos = $resultado->fetch_assoc();
        
        $contactos = json_decode($datos['contactos_notificar'] ?? '[]', true);
        
        return [
            'notificaciones_email' => (bool)$datos['notificaciones_email'],
            'contactos_notificar' => is_array($contactos) ? $contactos : []
        ];
    }

    public function Actualizar_Preferencias_Notificaciones($usuario, $recibir_notificaciones, $contactos_notificar) {
        $json_contactos = json_encode($contactos_notificar);
        
        $stmt = $this->conexion->prepare("UPDATE usuario SET 
            notificaciones_email = ?, 
            contactos_notificar = ? 
            WHERE usuario_usuario = ?");
        $stmt->bind_param("iss", $recibir_notificaciones, $json_contactos, $usuario);
        
        if($stmt->execute()) {
            // Actualizar sesión
            $_SESSION['notificaciones_config'] = [
                'notificaciones_email' => (bool)$recibir_notificaciones,
                'contactos_notificar' => $contactos_notificar
            ];
            return true;
        }
        
        return false;
    }

    private function buscarFotoUsuario($usuario){
        $ruta_fotos = 'C:/xampp/htdocs/CrudAdmin/app/views/fotos/';
        
        if (!is_dir($ruta_fotos)) {
            error_log("La carpeta de fotos no existe: ".$ruta_fotos);
            return null;
        }
        $patron = $usuario . '_*.*';
        $archivos = glob($ruta_fotos . $patron);
        
        if (!empty($archivos)) {
            return basename($archivos[0]);
        }
        
        return null;
    }
}
?>