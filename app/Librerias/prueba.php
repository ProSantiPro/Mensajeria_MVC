<?php
require_once 'Email_Notificaciones.php';

// Mostrar todos los errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    echo "<h2>Probando envío de correo...</h2>";
    
    $mailer = new EmailNotificaciones();
    
    echo "<p>Instancia de EmailNotificaciones creada correctamente</p>";
    
    $result = $mailer->enviarNotificacionMensaje(
        '2006strb@gmail.com',  // Cambia a un email que puedas verificar
        'Santiago', 
        'goku perez', 
        'Este es un mensaje de prueba'
    );
    
    if ($result) {
        echo "<p style='color:green;'>¡Correo enviado con éxito!</p>";
    } else {
        echo "<p style='color:red;'>Error al enviar, pero no se lanzó excepción</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red;'>Error capturado: " . $e->getMessage() . "</p>";
    echo "<pre>Detalles: " . print_r($e->getTrace(), true) . "</pre>";
}

echo "<h3>Fin de la prueba</h3>";
?>