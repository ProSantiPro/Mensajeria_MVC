<?php
require_once(__DIR__ . '/../../config/EmailConfig.php');
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailNotificaciones {
    private $mailer;

    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->configurarMailer();
    }

    private function configurarMailer() {
        $this->mailer = new PHPMailer(true);
        
        try {
            // Configuración SMTP
            $this->mailer->isSMTP();
            $this->mailer->Host = EmailConfig::HOST;
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = EmailConfig::USERNAME;
            $this->mailer->Password = EmailConfig::PASSWORD;
            $this->mailer->Port = EmailConfig::PORT;
            $this->mailer->SMTPSecure = EmailConfig::SECURE;
            $this->mailer->Timeout = EmailConfig::TIMEOUT;
            
            $this->mailer->SMTPDebug = 4;  // Cambiado de 2 a 4 para máximo detalle
    
            // Configurar output del debug para capturarlo
            $this->mailer->Debugoutput = function($str, $level) {
                error_log("PHPMailer DEBUG [$level]: $str");
                echo "<pre>PHPMailer DEBUG [$level]: $str</pre>"; // Mostrar en pantalla
            };
            // Configuración adicional importante
            $this->mailer->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];
            
            $this->mailer->setFrom(EmailConfig::FROM_EMAIL, EmailConfig::FROM_NAME);
            $this->mailer->isHTML(true);
            $this->mailer->CharSet = 'UTF-8';
            $this->mailer->Encoding = 'base64';
            
        } catch (Exception $e) {
            error_log("Error configurando PHPMailer: " . $e->getMessage());
            throw $e;
        }
    }


    public function enviarNotificacionMensaje($destinatario, $nombre_destinatario, $remitente, $contenido_mensaje) {
        try {
            // Validación adicional de email
            if (!filter_var($destinatario, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Email del destinatario no válido");
            }

            $this->mailer->clearAddresses();
            $this->mailer->addAddress($destinatario, $nombre_destinatario);
            $this->mailer->Subject = 'Nuevo mensaje de ' . $remitente;
            
            // Plantilla HTML mejorada
            $mensajeHTML = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <h3 style='color: #333;'>Hola $nombre_destinatario,</h3>
                    <p>Has recibido un nuevo mensaje de <strong>$remitente</strong>:</p>
                    <div style='background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                        $contenido_mensaje
                    </div>
                    <p style='text-align: center; margin-top: 20px;'>
                        <a href='http://localhost/Mensajeria_MVC/public/index.php' 
                           style='background: #4CAF50; color: white; padding: 10px 20px; 
                                  text-decoration: none; border-radius: 5px;'>
                            Responder al mensaje
                        </a>
                    </p>
                    <p style='font-size: 12px; color: #777; margin-top: 30px;'>
                        Este es un mensaje automático, por favor no respondas directamente a este correo.
                    </p>
                </div>
            ";
            
            $this->mailer->Body = $mensajeHTML;
            
            // Versión alternativa en texto plano
            $this->mailer->AltBody = "Hola $nombre_destinatario,\n\nHas recibido un mensaje de $remitente:\n\n$contenido_mensaje\n\nPuedes responder en: http://localhost/Mensajeria_MVC/public/index.php";
            
            $enviado = $this->mailer->send();
            
            if (!$enviado) {
                throw new Exception("No se pudo enviar el email: " . $this->mailer->ErrorInfo);
            }
            
            return true;
            
         } catch (Exception $e) {
            error_log("Error al enviar notificación: " . $e->getMessage());
            // No relanzamos la excepción para no interrumpir el flujo principal
            return false;
        }
    }

    public function enviarNotificacionArchivo($destinatario, $nombre_destinatario, $remitente, $nombre_archivo) {
        try {
            if (!filter_var($destinatario, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Email del destinatario no válido");
            }

            $this->mailer->clearAddresses();
            $this->mailer->addAddress($destinatario, $nombre_destinatario);
            $this->mailer->Subject = 'Nuevo archivo compartido por ' . $remitente;
            
            $mensajeHTML = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <h3 style='color: #333;'>Hola $nombre_destinatario,</h3>
                    <p>Has recibido un nuevo archivo de <strong>$remitente</strong>:</p>
                    <div style='background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                        Archivo: <strong>$nombre_archivo</strong>
                    </div>
                    <p style='text-align: center; margin-top: 20px;'>
                        <a href='http://localhost/Mensajeria_MVC/public/archivos.php' 
                        style='background: #4CAF50; color: white; padding: 10px 20px; 
                                text-decoration: none; border-radius: 5px;'>
                            Ver archivos compartidos
                        </a>
                    </p>
                </div>
            ";
            
            $this->mailer->Body = $mensajeHTML;
            $this->mailer->AltBody = "Hola $nombre_destinatario,\n\nHas recibido un archivo de $remitente:\n\n$nombre_archivo\n\nPuedes verlo en: http://localhost/Mensajeria_MVC/public/archivos.php";
            
            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Error al enviar notificación de archivo: " . $e->getMessage());
            return false;
        }
    }
}