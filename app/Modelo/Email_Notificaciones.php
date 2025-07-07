<?php
require_once(__DIR__ . '/../../config/EmailConfig.php');

class EmailNotificaciones {
    private $mailer;

    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->configurarMailer();
    }

    private function configurarMailer() {
        $this->mailer->isSMTP();
        $this->mailer->Host = EmailConfig::HOST;
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = EmailConfig::USERNAME;
        $this->mailer->Password = EmailConfig::PASSWORD;
        $this->mailer->SMTPSecure = EmailConfig::SECURE;
        $this->mailer->Port = EmailConfig::PORT;
        $this->mailer->setFrom(EmailConfig::FROM_EMAIL, EmailConfig::FROM_NAME);
        $this->mailer->isHTML(true);
    }

    public function enviarNotificacionMensaje($destinatario, $nombre_destinatario, $remitente, $contenido_mensaje) {
        try {
            $this->mailer->addAddress($destinatario, $nombre_destinatario);
            $this->mailer->Subject = 'Nuevo mensaje de ' . $remitente;
            
            $mensajeHTML = "
                <html>
                <head>
                    <title>Nuevo mensaje recibido</title>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background-color: #f8f9fa; padding: 15px; text-align: center; }
                        .content { padding: 20px; background-color: #ffffff; }
                        .footer { margin-top: 20px; padding: 10px; text-align: center; font-size: 12px; color: #6c757d; }
                        .btn { display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h2>Tienes un nuevo mensaje</h2>
                        </div>
                        <div class='content'>
                            <p>Hola $nombre_destinatario,</p>
                            <p>Has recibido un nuevo mensaje de <strong>$remitente</strong>:</p>
                            <blockquote style='background-color: #f8f9fa; padding: 15px; border-left: 5px solid #007bff; margin: 20px 0;'>
                                $contenido_mensaje
                            </blockquote>
                            <p style='text-align: center;'>
                                <a href='http://tudominio.com/Mensajeria_MVC/public/index.php' class='btn'>Ir al chat</a>
                            </p>
                        </div>
                        <div class='footer'>
                            <p>Si no deseas recibir más notificaciones, puedes cambiar tus preferencias en la configuración de tu perfil.</p>
                        </div>
                    </div>
                </body>
                </html>
            ";
            
            $this->mailer->Body = $mensajeHTML;
            $this->mailer->AltBody = "Hola $nombre_destinatario,\n\nHas recibido un nuevo mensaje de $remitente:\n\n$contenido_mensaje\n\nPuedes responder en: http://localhost/Mensajeria_MVC/public/index.php";
            
            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Error al enviar notificación por email: " . $this->mailer->ErrorInfo);
            return false;
        }
    }
}