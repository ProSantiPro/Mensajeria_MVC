<?php
$archivos_chat = $archivos_chat ?? [];
$usuario_seleccionado = $usuario_seleccionado ?? null;
$mensajes_chat = $mensajes_chat ?? [];
$saludo = $saludo ?? 'Bienvenido/a';
$ruta_base_fotos = 'http://localhost/CrudAdmin/app/views/fotos/';

$modeloUsuario = new Modelo_Usuario();
$datos_usuario = $modeloUsuario->Obtener_Datos_Usuario($_SESSION['usuario']['usuario_usuario']);
$foto_usuario = !empty($datos_usuario['usuario_foto']) ? 
    $ruta_base_fotos . $datos_usuario['usuario_foto'] : 
    $ruta_base_fotos . 'default.png';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>H-Zone</title>
    <link rel="icon" href="/Mensajeria_MVC/logo.png">
    <link rel="stylesheet" href="/Mensajeria_MVC/public/LosCSS/stilochat.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="contendedor">
        <div class="row">
            <div class="barra_izquierda">
                <div class="Bienvenida_usuario">
                    <div class="usuario-info">
                        <img src="<?= $foto_usuario ?>" alt="Tu foto de perfil" class="foto_perfil_header">
                        <div>
                            <h3><?= htmlspecialchars($saludo) ?>, <?= htmlspecialchars($_SESSION['usuario']['usuario_usuario'] ?? 'Usuario') ?></h3>
                        </div>
                    </div>
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
                        <?php if(!empty($usuario_seleccionado)): 
                            // Obtener foto del usuario seleccionado
                            $modeloUsuario = new Modelo_Usuario();
                            $datos_destinatario = $modeloUsuario->Obtener_Datos_Usuario($usuario_seleccionado);
                            $foto_destinatario = !empty($datos_destinatario['usuario_foto']) ? 
                                $ruta_base_fotos . $datos_destinatario['usuario_foto'] : 
                                $ruta_base_fotos . 'default.png';
                        ?>
                        <div class="chat_img">
                            <img src="<?= $foto_destinatario ?>" alt="Foto de <?= htmlspecialchars($usuario_seleccionado) ?>" class="foto_perfil_header_derecha">
                        </div>
                        <?php endif; ?>
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
                    <?php 
                        $borrador = $_SESSION['borradores'][$usuario_seleccionado] ?? '';
                        if (!empty($borrador)): ?>
                            <div class="borrador-container">
                                <div class="borrador-texto"><?php echo htmlspecialchars($borrador); ?></div>
                                <div class="borrador-botones">
                                    <button type="button" class="btn-borrador btn-borrador-eliminar" onclick="eliminarBorrador()">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                    <button type="button" class="btn-borrador" onclick="editarBorrador()">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>

                        <form method="post" enctype="multipart/form-data">
                            <input type="text" name="msg_contenido" class="form_control" 
                                placeholder="Escribe un mensaje..." 
                                autocomplete="off" 
                                value="">
                            
                            <button type="submit" name="enviar" class="btn" title="Enviar Mensaje">
                                <i class="fa fa-paper-plane"></i>
                            </button>
                            
                            <button type="button" class="btn btn-guardar" onclick="guardarBorrador()" title="Guardar Borrador">
                                <i class="fas fa-save"></i>
                            </button>
                        </form>
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
                    // Validar que el mensaje no esté vacío
                    const mensaje = document.querySelector('input[name="msg_contenido"]').value.trim();
                    if (mensaje === '') {
                        e.preventDefault();
                        alert('No puedes enviar un mensaje vacío');
                        return;
                    }
                    
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
        
        let isSaving = false;
        let currentChat = '<?php echo $usuario_seleccionado ?? ""; ?>';

        function guardarBorrador() {
            if (isSaving) return;
            isSaving = true;
            
            const mensajeInput = document.querySelector('input[name="msg_contenido"]');
            const mensaje = mensajeInput.value.trim();
            
            if (mensaje === '') {
                alert('No puedes guardar un mensaje vacío');
                isSaving = false;
                return;
            }
            
            const btnGuardar = document.querySelector('.btn-guardar');
            btnGuardar.disabled = true;
            btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            fetch('guardar_borrador.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `destinatario=${encodeURIComponent(currentChat)}&mensaje=${encodeURIComponent(mensaje)}`
            }).then(response => {
                if (response.ok) {
                    mostrarNotificacion('Borrador guardado correctamente');
                    mensajeInput.value = '';
                    actualizarVistaBorrador(mensaje);
                }
            }).catch(error => {
                console.error('Error al guardar borrador:', error);
                mostrarNotificacion('Error al guardar borrador', 'error');
            }).finally(() => {
                isSaving = false;
                const btnGuardar = document.querySelector('.btn-guardar');
                if (btnGuardar) {
                    btnGuardar.disabled = false;
                    btnGuardar.innerHTML = '<i class="fas fa-save"></i>';
                }
            });
        }

        function actualizarVistaBorrador(mensaje) {
            const borradorContainer = document.querySelector('.borrador-container');
            const inputMensaje = document.querySelector('input[name="msg_contenido"]');
            
            if (borradorContainer) {
                borradorContainer.querySelector('.borrador-texto').textContent = mensaje;
            } else {
                const nuevoBorrador = `
                    <div class="borrador-container">
                        <div class="borrador-texto">${mensaje}</div>
                        <div class="borrador-botones">
                            <button type="button" class="btn-borrador btn-borrador-eliminar" onclick="eliminarBorrador()">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            <button type="button" class="btn-borrador" onclick="editarBorrador()">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                        </div>
                    </div>`;
                document.querySelector('.chat_textbox').insertAdjacentHTML('afterbegin', nuevoBorrador);
            }
            
            // Si hay texto en el input, mostrar botón para cancelar/limpiar
            actualizarBotonLimpiar();
        }

        function eliminarBorrador() {
            if (!confirm('¿Estás seguro de que deseas eliminar este borrador?')) return;
            
            fetch('eliminar_borrador.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `destinatario=${encodeURIComponent(currentChat)}`
            }).then(response => {
                if (response.ok) {
                    document.querySelector('.borrador-container')?.remove();
                    mostrarNotificacion('Borrador eliminado');
                }
            }).catch(error => {
                console.error('Error al eliminar borrador:', error);
                mostrarNotificacion('Error al eliminar borrador', 'error');
            });
        }

        function editarBorrador() {
            const input = document.querySelector('input[name="msg_contenido"]');
            const borradorTexto = document.querySelector('.borrador-texto');
            
            if (borradorTexto) {
                input.value = borradorTexto.textContent;
                input.focus();
              
            }
        }

        function limpiarInput() {
            const input = document.querySelector('input[name="msg_contenido"]');
            input.value = '';
            actualizarBotonLimpiar();
        }

        function actualizarBotonLimpiar() {
            const input = document.querySelector('input[name="msg_contenido"]');
            const btnLimpiar = document.querySelector('.btn-limpiar');
            
            if (input.value.trim() !== '') {
                if (!btnLimpiar) {
                    const nuevoBoton = `
                        <button type="button" class="btn btn-limpiar" onclick="limpiarInput()" title="Limpiar mensaje">
                            <i class="fas fa-times"></i>
                        </button>`;
                    document.querySelector('.chat_textbox form').insertAdjacentHTML('beforeend', nuevoBoton);
                }
            } else {
                document.querySelector('.btn-limpiar')?.remove();
            }
        }

        function mostrarNotificacion(mensaje, tipo = 'success') {
            const notificacion = document.createElement('div');
            notificacion.className = `notificacion ${tipo}`;
            notificacion.textContent = mensaje;
            document.body.appendChild(notificacion);
            
            setTimeout(() => {
                notificacion.classList.add('mostrar');
                setTimeout(() => {
                    notificacion.classList.remove('mostrar');
                    setTimeout(() => notificacion.remove(), 300);
                }, 3000);
            }, 10);
        }

        // Observar cambios en el input
        const inputMensaje = document.querySelector('input[name="msg_contenido"]');
        if (inputMensaje) {
            let timeoutId;
            
            inputMensaje.addEventListener('input', function() {
                actualizarBotonLimpiar();
            });
            
            inputMensaje.addEventListener('blur', function() {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => {
                    if (this.value.trim() !== '' && !isSaving) {
                        guardarBorrador();
                    }
                }, 1000);
            });
            
            inputMensaje.addEventListener('focus', function() {
                clearTimeout(timeoutId);
            });
        }

        <?php if (!empty($usuario_seleccionado)): ?>
            setInterval(function() {
                fetch('obtener_mensajes.php?usuario_seleccionado=<?php echo $usuario_seleccionado; ?>')
                    .then(response => response.json())
                    .then(data => {
                        if (data.nuevosMensajes || data.nuevosArchivos || data.estadoCambiado) {
                            // Recargar la página si hay cambios
                            location.reload();
                        }
                    })
                    .catch(error => console.error("Error al obtener actualizaciones:", error));
            }, 5000); 
        <?php endif; ?>
    </script>       
    
</body>
</html>