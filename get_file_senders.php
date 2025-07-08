<?php
// get_file_senders.php
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/config.php';

header('Content-Type: application/json');

try {
    $database = new DataBase();
    $conn = $database->Conectar_db();
    
    $query = "SELECT sender_usuario, COUNT(*) as total_archivos 
              FROM archivos_chat 
              GROUP BY sender_usuario 
              ORDER BY total_archivos DESC";
    $result = $conn->query($query);
    
    $senders = [];
    while ($row = $result->fetch_assoc()) {
        $senders[$row['sender_usuario']] = $row['total_archivos'];
    }
    
    echo json_encode($senders);
    
    $conn->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>