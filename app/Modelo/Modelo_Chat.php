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
        
        if ($stmt->affected_rows > 0) {
            error_log("Mensaje insertado correctamente, enviando notificación si aplica");
            $this->enviarNotificacionSiAplica($sender, $receiver, $content);
            return true;
        }
        
        error_log("No se insertó ningún mensaje (affected_rows = 0)");
        return false;
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
    public function Obtener_Estado_Usuario($usuario) {
        $stmt = $this->conexion->prepare("SELECT login_status FROM usuarios WHERE usuario_usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();
        return $fila['login_status'] ?? 'logout';
    }

    private function enviarNotificacionSiAplica($sender, $receiver, $content) {
        try {
            require_once(__DIR__ . '/../../config/EmailConfig.php');
            require_once(__DIR__ . '/../Librerias/Email_Notificaciones.php');
            require_once(__DIR__ . '/../Modelo/Modelo_Usuario.php');
            
            $modeloUsuario = new Modelo_Usuario();
            
            // 1. Verificar si el receptor quiere notificaciones
            $preferencias = $modeloUsuario->Obtener_Preferencias_Notificaciones($receiver);
            
            if (!$preferencias['notificaciones_email']) {
                error_log("Usuario $receiver tiene notificaciones por email desactivadas");
                return;
            }
            
            // 2. Verificar si el sender está en la lista de contactos para notificar
            if (!empty($preferencias['contactos_notificar']) && 
                !in_array($sender, $preferencias['contactos_notificar'])) {
                error_log("Usuario $receiver no tiene habilitadas notificaciones para $sender");
                return;
            }
            
            // 3. Obtener datos del destinatario
            $destinatario = $modeloUsuario->Obtener_Datos_Usuario($receiver);
            if (empty($destinatario['usuario_email'])) {
                error_log("Usuario $receiver no tiene email configurado");
                return;
            }
            
            // 4. Enviar notificación
            $emailNotificaciones = new EmailNotificaciones();
            $resultado = $emailNotificaciones->enviarNotificacionMensaje(
                $destinatario['usuario_email'],
                $destinatario['usuario_usuario'],
                $sender,
                $content
            );
            
            if ($resultado) {
                error_log("Notificación enviada exitosamente a $receiver");
            }
            
        } catch (Exception $e) {
            error_log("Error en el proceso de notificación: " . $e->getMessage());
        }
    }
}
?>