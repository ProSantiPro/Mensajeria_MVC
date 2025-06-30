<?php
$archivos_chat = $archivos_chat ?? [];
$usuario_seleccionado = $usuario_seleccionado ?? null;
$mensajes_chat = $mensajes_chat ?? [];
$saludo = $saludo ?? 'Bienvenido/a';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link rel="stylesheet" href="/Mensajeria_MVC/public/LosCSS/stilochat.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="contendedor">
        <div class="row">
            <div class="barra_izquierda">
                <div class="Bienvenida_usuario">
                    <h3><?= htmlspecialchars($saludo) ?>, <?= htmlspecialchars($_SESSION['usuario']['usuario_usuario'] ?? 'Usuario') ?></h3>
                    <form method="post" action="" onsubmit="return Confirmar_Cierre_Sesion()">
                        <button type="submit" name="Desconectar" class="btn_desconectar">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </button>
                    </form>
                </div>
                
                <div class="chat_izquierda">
                    <ul>
                        <?php include(__DIR__.'/../includes/users_sidebar.php') ?>
                    </ul>
                </div>
            </div>

            <div class="barra_derecha">
                <div class="header_derecha">
                    <div class="header">
                        <div class="chat_img"></div>
                        <div class="detalles_derecha">
                            <h4><?= htmlspecialchars($usuario_seleccionado ?? 'Selecciona un usuario') ?></h4>
                            <a href="archivos.php" class="btn_opciones">
                                <i class="fa fa-file"></i> Administrar Archivos
                            </a>
                        </div>
                    </div>
                </div>

                <div id="scroll_al_final" class="contenido_chat">
                    <?php include(__DIR__.'/../includes/chat_mensajes.php') ?>
                </div>

                <?php if(!empty($usuario_seleccionado)): ?>
                <div class="chat_textbox">
                    <form method="post" enctype="multipart/form-data">
                        <input type="text" name="msg_contenido" class="form_control" placeholder="Escribe un mensaje..." required>
                        <button type="submit" name="enviar" class="btn">
                            <i class="fa fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const chatContainer = document.getElementById("scroll_al_final");
            
            // Scroll al final al cargar
            if(chatContainer) {
                scrollToBottom(chatContainer);
            }

            // Manejar el envío de formularios
            const form = document.querySelector(".chat_textbox form");
            if(form) {
                form.addEventListener("submit", function(e) {
                    // Opcional: Aquí podrías añadir AJAX para enviar sin recargar
                    setTimeout(() => scrollToBottom(chatContainer), 100);
                });
            }
        });

        function scrollToBottom(element) {
            if(element) {
                element.scrollTop = element.scrollHeight;
            }
        }

        function Confirmar_Cierre_Sesion() {
            return confirm('¿Estás seguro de que deseas cerrar sesión?');
        }
            

    </script>                
    
</body>
</html>