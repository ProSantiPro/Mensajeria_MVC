<?php
require_once(__DIR__ . '/../Modelo/Modelo_Archivos.php');

class Controlador_Archivos {
    private $modelo;

    public function __construct() {
        require_once(__DIR__ . '/../../config/session.php');
        
        if (!isset($_SESSION['usuario'])) {
            header("Location: /Mensajeria_MVC/public/Login.php");
            exit();
        }
        
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
        if ($this->modelo->Eliminar_Archivo($id, $_SESSION['usuario']['usuario'])) {
            header("Location: archivos.php?success=2");
            exit();
        } else {
            header("Location: archivos.php?error=2");
            exit();
        }
    }

    public function descargar($id) {
        $archivo = $this->modelo->Obtener_Archivo($id, $_SESSION['usuario']['usuario']);
        if ($archivo && file_exists(__DIR__ . '/../../public/archivos_upload/' . $archivo['ruta_archivo'])) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($archivo['sender_nombre_original']).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize(__DIR__ . '/../../public/archivos_upload/' . $archivo['ruta_archivo']));
            readfile(__DIR__ . '/../../public/archivos_upload/' . $archivo['ruta_archivo']);
            exit();
        } else {
            header("Location: archivos.php?error=3");
            exit();
        }
    }

    private function Cargar_Vista($vista, $datos = []) {
        extract($datos);
        require_once(__DIR__ . "/../Vista/archivos/{$vista}.php");
    }
}
?>