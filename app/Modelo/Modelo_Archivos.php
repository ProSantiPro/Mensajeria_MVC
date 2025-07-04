<?php
require_once(__DIR__ . '/../../config/Database.php');

class Modelo_Archivos {
    private $conexion;

    public function __construct() {
        $database = new DataBase();
        $this->conexion = $database->Conectar_db();
    }

    public function Subir_Archivo($sender, $receiver, $archivo){
        $directorio = __DIR__ . '/../../public/archivos_upload/';
        
        if (!is_dir($directorio)) {
            if (!mkdir($directorio, 0777, true)) {
                throw new Exception("No se pudo crear el directorio de archivos");
            }
        }
        $directorio_usuario = $directorio . $receiver . '/';
        if(!is_dir($directorio_usuario)){
            if(!mkdir($directorio_usuario, 0777, true)){
                throw new Exception("No se pudo crear el directorio del usuario");
            }
        }
        
        $nombre_original = basename($archivo['name']);
        $extension = pathinfo($nombre_original, PATHINFO_EXTENSION);
        $nombre_unico = uniqid() . '.' . $extension;
        $ruta_destino = $directorio_usuario . $nombre_unico;

        $extensiones_permitidas = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif', 'zip', 'rar'];
        if (!in_array(strtolower($extension), $extensiones_permitidas)) {
            throw new Exception("Tipo de archivo no permitido");
        }

        if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
            $stmt = $this->conexion->prepare("INSERT INTO archivos_chat 
                (sender_usuario, receiver_usuario, nombre_archivo, sender_nombre_original, 
                ruta_archivo, tipo_archivo, tamano_archivo, fecha_subida) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");

            $stmt->bind_param("ssssssi", 
                $sender,
                $receiver,
                $nombre_unico,
                $nombre_original,
                $nombre_unico, // Aquí guardamos solo el nombre del archivo
                $archivo['type'],
                $archivo['size']
            );
            
            return $stmt->execute();
        }
        
        throw new Exception("No se pudo mover el archivo subido");
    }

    public function Obtener_Archivos_Usuario($usuario){
        try {
            $query = "SELECT *, CASE WHEN sender_usuario = ? THEN CONCAT('Enviado a ', receiver_usuario)
            ELSE CONCAT('Recibido de ', sender_usuario) END AS descripcion_archivo FROM archivos_chat WHERE
            sender_usuario = ? OR receiver_usuario = ? ORDER BY fecha_subida DESC";

            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("sss", $usuario, $usuario, $usuario);
            $stmt->execute();
            $resultado = $stmt->get_result();
            return $resultado->fetch_all(MYSQLI_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error en obtenerArchivosUsuario: " . $e->getMessage());
            return [];
        }
    

    }

    public function Obtener_Archivo($id, $usuario) {
        $stmt = $this->conexion->prepare("SELECT * FROM archivos_chat WHERE id = ? AND (sender_usuario = ? OR receiver_usuario = ?)");
        $stmt->bind_param("iss", $id, $usuario, $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $archivo = $resultado->fetch_assoc();

        if ($archivo) {
            $archivo['ruta'] = __DIR__ . '/../../public/archivos_upload/' . 
                            $archivo['receiver_usuario'] . '/' . $archivo['ruta_archivo'];
        }
        return $archivo;
    }

    public function Eliminar_Archivo($id, $usuario){
        $archivo = $this->Obtener_Archivo($id, $usuario);
        if ($archivo && file_exists($archivo['ruta'])){
            unlink($archivo['ruta']);
            $stmt = $this->conexion->prepare("DELETE FROM archivos_chat WHERE id = ? AND 
            sender_usuario = ?");
            $stmt->bind_param("is", $id, $usuario);
            return $stmt->execute();
        }
        return false;
    }

    public function Formatear_Tamano($bytes){
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return '1 byte';
        } else {
            return '0 bytes';
        }
    }
    
    public function Obtener_Archivos_Compartidos($usuario1, $usuario2) {
        error_log("Buscando archivos entre: $usuario1 y $usuario2"); // Debug
        
        $query = "SELECT * FROM archivos_chat 
                WHERE (sender_usuario = ? AND receiver_usuario = ?)
                OR (sender_usuario = ? AND receiver_usuario = ?)
                ORDER BY fecha_subida ASC";
        
        $stmt = $this->conexion->prepare($query);
        if (!$stmt) {
            error_log("Error preparando consulta: " . $this->conexion->error);
            return [];
        }
        
        $stmt->bind_param("ssss", $usuario1, $usuario2, $usuario2, $usuario1);
        
        if (!$stmt->execute()) {
            error_log("Error ejecutando consulta: " . $stmt->error);
            return [];
        }
        
        $resultado = $stmt->get_result();
        $archivos = $resultado->fetch_all(MYSQLI_ASSOC);
        
        error_log("Archivos encontrados: " . count($archivos)); // Debug
        return $archivos;
    }
}

?>