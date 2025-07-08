<?php
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/config.php';

header('Content-Type: application/json');

try {
    $database = new DataBase();
    $conn = $database->Conectar_db();
    
    $query = "SELECT usuario_email FROM usuario";
    $result = $conn->query($query);
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    
    echo json_encode($users);
    
    $conn->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>