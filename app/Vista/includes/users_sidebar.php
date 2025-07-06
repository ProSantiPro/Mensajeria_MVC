<?php
if (!empty($usuarios_totales)) {
    $usuarios_mostrados = [];
    $ruta_base_fotos = 'http://localhost/CrudAdmin/app/views/fotos/';
    $modeloUsuario = new Modelo_Usuario();

    foreach ($usuarios_totales as $usuario) {
        $datos_usuario = $modeloUsuario->Obtener_Datos_Usuario($usuario['usuario_usuario']);
        $username = htmlspecialchars($usuario['usuario_usuario'] ?? '');
        $login_status = $usuario['login_status'] ?? 'logout';
        $mensajes_no_leidos = ($_SESSION['usuario']['usuario_usuario'] == $username) ? 
            (new Modelo_Chat())->Obtener_Mensajes_No_Leidos($username) : 0;
        $ultimo_mensaje = (new Modelo_Chat())->Obtener_Ultimo_Mensaje($_SESSION['usuario']['usuario_usuario'], $username);
        $texto_mensaje = $ultimo_mensaje ? substr($ultimo_mensaje['msg_contenido'], 0, 30) . (strlen($ultimo_mensaje['msg_contenido']) > 30 ? '...' : ''): "No hay mensajes";
        $usuario_email = urlencode($usuario['usuario_email'] ?? '');
        $pagina_actual = htmlspecialchars($_SERVER['PHP_SELF']);
        $foto_perfil = !empty($datos_usuario['usuario_foto']) ? 
            $ruta_base_fotos . $datos_usuario['usuario_foto'] : 
            $ruta_base_fotos . 'default.png';

       if (in_array($username, $usuarios_mostrados) || $username == ($_SESSION['usuario']['usuario_usuario'] ?? '')) {
            continue;
        }
        
        $usuarios_mostrados[] = $username;
        ?>  
        <?php
            echo '<li class="usuario_item">';
            echo '<a href="'.$pagina_actual.'?correo_usuario='.$usuario_email.'" class="usuario_link">';
            echo '<div class="imagen_izq">';
            echo '<img src="'.$foto_perfil.'" alt="Foto de perfil" class="foto_perfil_sidebar">'; echo '</div>';
            echo '<div class="detalles_izq">';
            echo '<p>';
            echo $username;
            echo '<br>';    
            echo '<small>'.($login_status == 'Online' ? 'En Línea' : 'Desconectado').'</small>';
            
            // Mostrar badge solo si hay mensajes no leídos Y es el usuario actual
            if ($mensajes_no_leidos > 0 && $_SESSION['usuario']['usuario_usuario'] == $username) {
                echo '<span class="badge-notification">'.$mensajes_no_leidos.'</span>';
            }
            
            echo '</p>';
            echo '<small class="ultimo-mensaje">'.htmlspecialchars($texto_mensaje).'</small>';
            echo '</div>';
            echo '</a>';
            echo '</li>';
        }
    
        echo '<li class="usuario_item configuracion-item">';
        echo '<a href="configuracion.php" class="usuario_link">';
        echo '<div class="imagen_izq">';
        echo '<i class="fas fa-cog" style="font-size: 1.5em; padding: 10px;"></i>';
        echo '</div>';
        echo '<div class="detalles_izq">';
        echo '<p>Configuración</p>';
        echo '</div>';
        echo '</a>';
        echo '</li>';
    
    }else {
        echo "<li>No hay usuarios disponibles</li>";
    }
?>
        
    