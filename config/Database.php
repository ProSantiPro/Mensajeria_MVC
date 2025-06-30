<?php
class DataBase {
    private $host = "localhost";
    private $usuario = "root";
    private $password = "";
    private $db = "crudadmin";
    private $conexion;

    public function Conectar_db() {
        $this->conexion = null;
        
        try {
            $this->conexion = new mysqli($this->host, $this->usuario, $this->password, $this->db);
            
            if ($this->conexion->connect_errno) {
                throw new Exception("Error de conexión a la base de datos: " . $this->conexion->connect_error);
            }
            
            // Establecer el conjunto de caracteres a utf8
            $this->conexion->set_charset("utf8mb4");
            
        } catch (Exception $e) {
            error_log("Error de conexión: " . $e->getMessage());
            die("Error al conectar con la base de datos. Por favor intente más tarde.");
        }
        
        return $this->conexion;
    }
}
?>