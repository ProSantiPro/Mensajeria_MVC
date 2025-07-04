<?php
require_once(__DIR__ . '/../../config/Database.php');

class Modelo_Chat {
    private $conexion;

    public function __construct() {
        $database = new DataBase();
        $this->conexion = $database->Conectar_db();

        if($this->conexion->connect_error){
            error_log("Error de conexion en Modelo_chat: ". $this->conexion->connect_error);
            
        }
    }

    public function Obtener_Mensajes_Chat($usuario_actual, $usuario_destino){
        error_log("Buscando mensajes entre: $usuario_actual y $usuario_destino");
        
        $query = "SELECT * FROM chat_usuarios 
                 WHERE (sender_usuario = ? AND receiver_usuario = ?)
                 OR (sender_usuario = ? AND receiver_usuario = ?) 
                 ORDER BY msg_fecha ASC";

        $stmt = $this->conexion->prepare($query);
        if (!$stmt) {
            error_log("Error en prepararcion de obtener mensajes: ". $this->conexion->error);
            return[];
        }

        $stmt->bind_param("ssss", $usuario_actual, $usuario_destino, $usuario_destino, $usuario_actual);
        if(!$stmt->execute()){
            error_log("Error al ejecutar Obtener Mensajes: ". $stmt->error);
            return[];
        }
        $resultado = $stmt->get_result();
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    public function Obtener_Mensajes_Totales($usuario_actual, $usuario_destino){
        $stmt = $this->conexion->prepare("SELECT COUNT(*) as total FROM chat_usuarios 
        WHERE (sender_usuario = ? AND receiver_usuario = ?) OR (receiver_usuario = ?
        AND sender_usuario = ?)");
        $stmt->bind_param("ssss", $usuario_actual, $usuario_destino, $usuario_actual, $usuario_destino);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $row = $resultado->fetch_assoc();
        return $row['total'];
    }

    public function Mensajes_Vistos($sender, $receiver){
        $stmt = $this->conexion->prepare("UPDATE chat_usuarios SET msg_status = 'read'
        WHERE sender_usuario = ? AND receiver_usuario = ?");

        $stmt->bind_param("ss", $sender, $receiver);
        return $stmt->execute();
    }

    public function Insertar_Mensaje($sender, $receiver, $content) {
        // Debug: Verificar parámetros recibidos
        error_log("Intentando insertar mensaje - De: $sender, Para: $receiver, Contenido: $content");
        
        if ($sender == $receiver) {
            error_log("Error: Intento de mensaje a sí mismo");
            return false;
        }

         $stmt = $this->conexion->prepare("INSERT INTO chat_usuarios (sender_usuario, receiver_usuario, msg_contenido, msg_status, 
         msg_fecha) VALUES (?, ?, ?, 'unread', NOW())");
        
        if (!$stmt) {
            error_log("Error preparando consulta: " . $this->conexion->error);
            return false;
        }
        
        $stmt->bind_param("sss", $sender, $receiver, $content);
        
        if (!$stmt->execute()) {
            error_log("Error ejecutando inserción: " . $stmt->error);
            return false;
        }
        
        error_log("Mensaje insertado correctamente");
        return true;
    }

    public function Obtener_Mensajes_No_Leidos($usuario_destino){
        $stmt = $this->conexion->prepare("SELECT COUNT(*) as total FROM chat_usuarios
        WHERE receiver_usuario = ? AND msg_status = 'unread' ");
        $stmt->bind_param("s", $usuario_destino);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $row = $resultado->fetch_assoc();
        return $row['total'] ?? 0;
    }

    public function Obtener_Ultimo_Mensaje($usuario1, $usuario2){
        $stmt = $this->conexion->prepare("SELECT * FROM chat_usuarios WHERE(sender_usuario = ?
        AND receiver_usuario = ?) OR (sender_usuario = ? AND receiver_usuario = ?)
        ORDER BY msg_fecha DESC LIMIT 1");
        $stmt->bind_param("ssss", $usuario1, $usuario2, $usuario2, $usuario1);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }


}
?>