<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$nombre_usuario = $nombre_usuario ?? 'Usuario';
$archivos = $archivos ?? [];
$mensaje_exito = $mensaje_exito ?? null;
$mensaje_error = $mensaje_error ?? null;
require_once(__DIR__ . '/../../Modelo/Modelo_Usuario.php');
require_once(__DIR__ . '/../../Modelo/Modelo_Archivos.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/Mensajeria_MVC/public/LosCSS/estilos_archivos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Administrar Archivos</title>
</head>
<body>
    <div class="contenedor-archivos">
        <div class="row_file">
            <div class="barra_izquierda_file">
                <div class="Bienvenida_usuario_file">
                    <h3><?php echo htmlspecialchars($_SESSION['usuario']['usuario_usuario'] ?? 'Usuario'); ?></h3>
                    <a href="index.php" class="btn_desconectar">
                        <i class="fas fa-arrow-left"></i> Volver al Chat
                    </a>
                </div>
            </div>

            <div class="barra_derecha">
                <div class="header_derecha_file">
                    <h2><i class="fas fa-folder-open"></i> Mis Archivos</h2>
                </div>

                <div class="contenido_chat_file">
                    <?php if(isset($_GET['success'])): ?>
                        <div class="mensaje-exito">
                            <i class="fas fa-check-circle"></i>
                            <?php
                            switch ($_GET['success']) {
                                case 1:
                                    echo "Archivo subido correctamente";
                                    break;
                                case 2:
                                    echo "Archivo eliminado correctamente";
                                    break;
                                }
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['error'])): ?>
                        <div class="mensaje-error">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php
                            $errores = [
                                1 => "Error al subir el archivo",
                                2 => "Error al eliminar el archivo",
                                3 => "Error al descargar el archivo o no existe",
                                4 => "Error general del sistema",
                                5 => "Debes seleccionar un usuario destino",
                                6 => "Error en la subida del archivo",
                                7 => "El archivo excede el tamaño máximo de 10MB",
                                8 => "No se ha seleccionado ningún archivo"
                            ];
                            echo $errores[$_GET['error']] ?? "Error desconocido";
                            ?>
                        </div>
                    <?php endif; ?>

                    <div class="form-subir-archivo">
                        <h3><i class="fas fa-cloud-upload-alt"></i> Subir Nuevo Archivo</h3>
                        <form method="post" action="archivos.php?action=subir" enctype="multipart/form-data">
                            <div class = "form-group">
                                <label for="receiver"> <i class = "fas fa-user"></i>Enviar a: </label>
                                <select name="receiver" id="receiver" required>
                                    <option value="">Seleccionar Usuario Destino</option>
                                    <?php
                                    $usuarios = (new Modelo_Usuario())->Obtener_Usuarios();
                                    foreach($usuarios as $usuario):
                                        if($usuario['usuario_usuario'] != $_SESSION['usuario']['usuario_usuario']):
                                    ?>
                                        <option value="<?= htmlspecialchars($usuario['usuario_usuario'])?>">
                                            <?= htmlspecialchars($usuario['usuario_usuario']) ?>
                                        </option>
                                    <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </select>
                            </div>
                        
                            <div class="form-group">
                                <label for="archivo"><i class="fas fa-file"></i> Seleccionar Archivo (Max 10MB):</label>
                                <input type="file" name="archivo" id="archivo" required>
                            </div>
                            <button type="submit" class="btn">
                                <i class="fas fa-upload"></i> Subir Archivo
                            </button>
                        </form>
                    </div>

                    <div class="tabla-archivos">
                        <h3><i class="fas fa-files"></i> Tus Archivos Subidos</h3>
                        <?php if(empty($archivos)): ?>
                            <p class="texto-centro">No has subido ningún archivo aún.</p>
                        <?php else: ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Nombre del Archivo</th>
                                        <th>Descripcion</th>
                                        <th>Tamaño</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($archivos as $archivo): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($archivo['sender_nombre_original']); ?></td>
                                            <td><?php echo htmlspecialchars($archivo['descripcion_archivo']); ?></td>
                                            <td><?php echo (new Modelo_Archivos())->Formatear_Tamano($archivo['tamano_archivo']);  ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($archivo['fecha_subida'])); ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($archivo['fecha_subida'])); ?></td>
                                            <td>
                                                <a href="archivos.php?action=descargar&id=<?php echo $archivo['id']; ?>" class="btn-descargar">
                                                    <i class="fas fa-download"></i> Descargar
                                                </a>

                                                <?php if($archivo['sender_usuario'] == $_SESSION['usuario']['usuario_usuario']): ?>
                                                    <a href="archivos.php?action=eliminar&id=<?php echo $archivo['id']; ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar este archivo?')">
                                                        <i class="fas fa-trash"></i> Eliminar
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>    
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>