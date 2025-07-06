<?php
require_once(__DIR__ . '/../Modelo/Modelo_Archivos.php');

class Controlador_Archivos {
    private $modelo;
    private $conexion;

    public function __construct() {
        require_once(__DIR__ . '/../../config/session.php');
        
        if (!isset($_SESSION['usuario'])) {
            header("Location: /Mensajeria_MVC/public/Login.php");
            exit();
        }

        require_once(__DIR__ . '/../../config/Database.php');
        $database = new DataBase();
        $this->conexion = $database->Conectar_db();
        
        $this->modelo = new Modelo_Archivos();
    }

    public function index() {
        $usuario_actual = $_SESSION['usuario']['usuario_usuario'] ?? '';
        
        if (empty($usuario_actual)) {
            header("Location: /Mensajeria_MVC/public/Login.php");
            exit();
        }

        $archivos = $this->modelo->Obtener_Archivos_Usuario($usuario_actual);
        $this->Cargar_Vista('index', [
            'nombre_usuario' => $usuario_actual,
            'archivos' => $archivos,
            'mensaje_exito' => $_GET['success'] ?? null,
            'mensaje_error' => $_GET['error'] ?? null
        ]);
    } 
    
    public function subir() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {
            // Verificar errores de subida
            if ($_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
                header("Location: archivos.php?error=6");
                exit();
            }

            $receiver = $_POST['receiver'] ?? null;
            
            if (empty($receiver)) {
                header("Location: archivos.php?error=5");
                exit();
            }

            // Validar tamaño máximo (10MB)
            if ($_FILES['archivo']['size'] > 10485760) {
                header("Location: archivos.php?error=7");
                exit();
            }

            // Validar que sea un archivo válido
            if (!is_uploaded_file($_FILES['archivo']['tmp_name'])) {
                header("Location: archivos.php?error=6");
                exit();
            }

            try {
                $resultado = $this->modelo->Subir_Archivo(
                    $_SESSION['usuario']['usuario_usuario'], 
                    $receiver, 
                    $_FILES['archivo']
                );
                
                 if ($resultado) {
                    $this->notificarNuevoArchivo($receiver);
                    header("Location: archivos.php?success=1");
                    exit();
                } else {
                    header("Location: archivos.php?error=1");
                    exit();
                }
            } catch (Exception $e) {
                error_log("Error al subir archivo: " . $e->getMessage());
                header("Location: archivos.php?error=4");
                exit();
            }
        } else {
            header("Location: archivos.php?error=8");
            exit();
        }
        
    }

    public function eliminar($id) {
        $usuario_actual = $_SESSION['usuario']['usuario_usuario'] ?? '';
        $archivo = $this->modelo->Obtener_Archivo($id, $usuario_actual);
        
        if ($archivo && $archivo['sender_usuario'] == $usuario_actual) {
            $ruta_completa = __DIR__ . '/../../public/archivos_upload/' . 
                            $archivo['receiver_usuario'] . '/' . $archivo['ruta_archivo'];
            
            if (file_exists($ruta_completa)) {
                unlink($ruta_completa);
            }
            
            $stmt = $this->conexion->prepare("DELETE FROM archivos_chat WHERE id = ? AND sender_usuario = ?");
            $stmt->bind_param("is", $id, $usuario_actual);
            
            if ($stmt->execute()) {
                header("Location: archivos.php?success=2");
                exit();
            }
        }
        
        header("Location: archivos.php?error=2");
        exit();
    }

    public function descargar($id) {
        $usuario_actual = $_SESSION['usuario']['usuario_usuario'] ?? '';
        $archivo = $this->modelo->Obtener_Archivo($id, $usuario_actual);

        if ($archivo) {
        $ruta_completa = __DIR__ . '/../../public/archivos_upload/' . 
        $archivo['receiver_usuario'] . '/' . $archivo['ruta_archivo'];
        
        if (file_exists($ruta_completa)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($archivo['sender_nombre_original']).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($ruta_completa));
            readfile($ruta_completa);
            exit();
            }
        }
    
        header("Location: archivos.php?error=3");
        exit();
    }

    private function Cargar_Vista($vista, $datos = []) {
        extract($datos);
        require_once(__DIR__ . "/../Vista/archivos/{$vista}.php");
    }

    private function notificarNuevoArchivo($receiver) {
        error_log("Nuevo archivo enviado a: " . $receiver);
    }
}
?>