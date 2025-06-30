<?php
if (!empty($usuarios_totales)) {
    $usuarios_mostrados = [];

    foreach ($usuarios_totales as $usuario) {
        $username = htmlspecialchars($usuario['usuario_usuario'] ?? '');
        $login_status = $usuario['login_status'] ?? 'logout';
        $usuario_email = urlencode($usuario['usuario_email'] ?? '');
        $pagina_actual = htmlspecialchars($_SERVER['PHP_SELF']);
        
       if (in_array($username, $usuarios_mostrados) || $username == ($_SESSION['usuario']['usuario_usuario'] ?? '')) {
            continue;
        }
        
        $usuarios_mostrados[] = $username;
        ?>  
        <li class="usuario_item">
            <a href="<?php echo $pagina_actual; ?>?correo_usuario=<?php echo $usuario_email; ?>" class="usuario_link">
                <div class="imagen_izq">
                    <!-- Espacio para imagen de perfil -->
                </div>
                <div class="detalles_izq">
                    <p>
                        <?php echo $username; ?>
                        <br>    
                        <small><?php echo ($login_status == 'Online' ? 'En Línea' : 'Desconectado'); ?></small>
                    </p>
                </div>
            </a>
        </li>
        <?php
    } 
} else {
    echo "<li>No hay usuarios disponibles</li>";
}
?>