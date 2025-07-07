<?php
// Verificación de sesión al inicio
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    header("Location: Login.php");
    exit();
}

require_once(__DIR__ . '/../../Controlador/Controlador_Configuracion.php');
$controlador = new Controlador_Configuracion();

$datos = $controlador->mostrarConfiguracion();

$preferencias = $datos['preferencias'] ?? [
    'notificaciones_email' => true,
    'contactos_notificar' => []
];

if (!is_array($preferencias['contactos_notificar'])) {
    $preferencias['contactos_notificar'] = [];
}

$usuarios = isset($datos['usuarios']) && is_array($datos['usuarios']) ? $datos['usuarios'] : [];
$usuario_actual = $datos['usuario_actual'] ?? '';
$ruta_base_fotos = 'http://localhost/CrudAdmin/app/views/fotos/'; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Notificaciones</title>
    <link rel="stylesheet" href="/Mensajeria_MVC/public/LosCSS/estilos_configuracion.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="/Mensajeria_MVC/logo.png">
</head>
<body class="contendedor">
    <div class="configuracion-container">
         <a href="index.php" class="btn-volver-chat">
            <i class="fas fa-arrow-left"></i> Volver al Chat
        </a>
        <?php if (!empty($_SESSION['mensaje_exito'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($_SESSION['mensaje_exito']) ?>
                <?php unset($_SESSION['mensaje_exito']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($_SESSION['mensaje_error'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($_SESSION['mensaje_error']) ?>
                <?php unset($_SESSION['mensaje_error']); ?>
            </div>
        <?php endif; ?>

        <div class="config-header">
            <div class="icono">
                <i class="fas fa-bell"></i>
            </div>
            <h2>Configuración de Notificaciones</h2>
        </div>

        <form method="post" action="configuracion.php?action=guardar_notificaciones" id="form-config-notificaciones" class="needs-validation" novalidate>
            <!-- Sección de notificaciones por email -->
            <div class="config-section">
                <h3><i class="fas fa-envelope"></i> Notificaciones por Email</h3>
                <div class="switch-container">
                    <label class="switch">
                        <input type="checkbox" name="recibir_notificaciones" value="1" 
                            id="notificaciones-email" <?= $preferencias['notificaciones_email'] ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                    <span>Recibir notificaciones por correo electrónico</span>
                </div>
            </div>

            <!-- Sección de contactos para notificar -->
            <div class="config-section">
                <h3><i class="fas fa-users"></i> Contactos para Notificar</h3>
                <p>Selecciona de qué contactos deseas recibir notificaciones</p>
                
                <div class="contactos-grid" id="contactos-grid">
                    <?php foreach($usuarios as $usuario): 
                        if($usuario['usuario_usuario'] == $usuario_actual) continue;
                        
                        $foto = !empty($usuario['usuario_foto']) ? 
                            $ruta_base_fotos . $usuario['usuario_foto'] : 
                            $ruta_base_fotos . 'default.png';
                        
                        $estaSeleccionado = in_array($usuario['usuario_usuario'], $preferencias['contactos_notificar']);
                    ?>
                    <div class="contacto-item" data-user="<?= htmlspecialchars($usuario['usuario_usuario']) ?>">
                        <input type="checkbox" 
                            name="contactos_notificar[]" 
                            id="contacto-<?= htmlspecialchars($usuario['usuario_usuario']) ?>" 
                            value="<?= htmlspecialchars($usuario['usuario_usuario']) ?>"
                            <?= $estaSeleccionado ? 'checked' : '' ?>
                            class="contacto-checkbox hidden">
                        <label for="contacto-<?= htmlspecialchars($usuario['usuario_usuario']) ?>" 
                            class="contacto-label <?= $estaSeleccionado ? 'selected' : '' ?>">
                            <img src="<?= htmlspecialchars($foto) ?>" 
                                alt="<?= htmlspecialchars($usuario['usuario_usuario']) ?>" 
                                class="contacto-avatar">
                            <div class="contacto-info">
                                <p><?= htmlspecialchars($usuario['usuario_usuario']) ?></p>
                                <small><?= $usuario['login_status'] == 'Online' ? 'En línea' : 'Desconectado' ?></small>
                            </div>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" class="btn-guardar" id="btn-guardar">
                <i class="fas fa-save"></i> <span class="btn-text">Guardar Configuración</span>
            </button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Manejar el clic en las etiquetas de contacto
            document.querySelectorAll('.contacto-label').forEach(label => {
                label.addEventListener('click', function(e) {
                    // Prevenir el comportamiento por defecto
                    e.preventDefault();
                    
                    // Obtener el checkbox asociado
                    const checkbox = this.previousElementSibling;
                    
                    // Alternar el estado del checkbox
                    checkbox.checked = !checkbox.checked;
                    
                    // Actualizar la clase visual inmediatamente
                    this.classList.toggle('selected', checkbox.checked);
                    
                    // Efecto de animación para feedback táctil
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 200);
                });
            });

            // Manejar el envío del formulario
            const form = document.getElementById('form-config-notificaciones');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const btn = this.querySelector('.btn-guardar');
                    if (btn) {
                        btn.classList.add('guardando');
                        btn.disabled = true;
                        btn.querySelector('.btn-text').textContent = 'Guardando...';
                    }
                });
            }
            
            // Ocultar las alertas después de 3 segundos
            const alert = document.querySelector('.alert');
            if (alert) {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.remove();
                    }, 300);
                }, 3000);
            }
        });
    </script>
</body>
</html>