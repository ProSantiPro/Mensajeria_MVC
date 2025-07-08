<?php

if(isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
$errorMessages = [
    1 => "Usuario o contraseña incorrectos",
    2 => "Error en el sistema. Por favor, intente más tarde",
    3 => "No puedes iniciar sesión sin un correo electrónico registrado"
];

$error = "";
if (isset($_GET['error'])) {
    $errorCode = (int)$_GET['error']; // Convertir a entero para seguridad
    $error = $errorMessages[$errorCode] ?? "Error desconocido";
}

// Profe, El usuario que esta registrado por defecto es Admin01, la clave es Administrador.
?>


<!DOCTYPE html>
<html lang="es">
    
     <link rel="stylesheet" href="../public/LosCSS/EstilosLogin.css"> 
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">   
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/Mensajeria_MVC/logo.png">
    <title>Iniciar Sesión</title>
   
</head>
<body>

    <a href="../index.html" class="btn-volver-inicio">
        <i class="fas fa-arrow-left"></i> Volver al Inicio
    </a>

    <img src="../app/vista/Imagenes/descarga.png" alt="Logo de la empresa" class="logo">

    <div class="left-spacer">
    </div>
    <div class="right-side-wrapper">
        <div class="login-container">
            <h2>Iniciar Sesión</h2>
            
            <?php if(isset($error) && $error): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="post" action="../app/Controlador/Controlador_Login.php">
                <div class="form-group">
                    <label for="usuario">Usuario:</label>
                    <input 
                        type="text" 
                        id="usuario" 
                        name="usuario" 
                        required 
                        placeholder="Ingrese su usuario"
                        autocomplete=off
                    >
                    
                </div>
                   
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        placeholder="Ingrese su contraseña"
                    >
                    <button type="button" class="toggle-password" aria-label="Mostrar contraseña">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                
                <button type="submit" class="btn-login">Iniciar Sesión</button>
            </form>
        </div>
    </div>
    <script>
        
        document.querySelector('.toggle-password').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
                this.setAttribute('aria-label', 'Ocultar contraseña');
            } else {
                passwordInput.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
                this.setAttribute('aria-label', 'Mostrar contraseña');
            }
        });
    </script>
</body>
</html>