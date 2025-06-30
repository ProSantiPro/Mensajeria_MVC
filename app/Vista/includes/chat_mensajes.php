<?php
require_once(__DIR__ . '/../../Modelo/Modelo_Archivos.php');

function obtenerIconoArchivo($nombreArchivo) {
    $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
    
    $iconos = [
        'pdf' => 'fa-file-pdf',
        'doc' => 'fa-file-word',
        'docx' => 'fa-file-word',
        'xls' => 'fa-file-excel',
        'xlsx' => 'fa-file-excel',
        'jpg' => 'fa-file-image',
        'jpeg' => 'fa-file-image',
        'png' => 'fa-file-image',
        'gif' => 'fa-file-image',
        'zip' => 'fa-file-archive',
        'rar' => 'fa-file-archive'
    ];
    
    return 'fas ' . ($iconos[$extension] ?? 'fa-file');
}

function formatearTamanio($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        return $bytes . ' bytes';
    } elseif ($bytes == 1) {
        return '1 byte';
    } else {
        return '0 bytes';
    }
}

$usuario_logeado = $_SESSION['usuario']['usuario_usuario'] ?? '';

if(empty($usuario_seleccionado)) {
    echo '<div class="texto_centro"><p>Selecciona un usuario para comenzar a chatear</p></div>';
    return;
}

if($usuario_logeado === $usuario_seleccionado) {
    echo '<div class="texto_centro"><p>No puedes chatear contigo mismo</p></div>';
    return;
}

if (empty($mensajes_chat) && empty($archivos_compartidos)) {
    echo '<div class="no-messages">';
    echo '<p class="texto_centro">Comienza una conversación con '.htmlspecialchars($usuario_seleccionado).'</p>';
    echo '</div>';
} else {
    echo '<div class="mensajes_contenedor" id="mensajes-contenedor">';
    
    // Mensajes de texto
    foreach($mensajes_chat as $msg) {
        $sender_user = htmlspecialchars($msg['sender_usuario']);
        $receiver_user = htmlspecialchars($msg['receiver_usuario']);
        $msg_content = htmlspecialchars($msg['msg_contenido']);
        $msg_date = date("d M H:i", strtotime($msg['msg_fecha']));
        
        
        if ($usuario_logeado === $sender_user) {
            echo '<div class="msg_derecha">';
            echo '<p>'.$msg_content.'</p>';
            echo '<span class="time">Tú <small>'.$msg_date.'</small></span>';
            echo '</div>';
        } 
       
        elseif ($usuario_seleccionado === $sender_user) {
            echo '<div class="msg_izquierda">';
            echo '<p>'.$msg_content.'</p>';
            echo '<span class="time">'.$sender_user.' <small>'.$msg_date.'</small></span>';
            echo '</div>';
        }
    }
    
    // Archivos compartidos
    $modeloArchivos = new Modelo_Archivos();
    $archivosCompartidos = $modeloArchivos->Obtener_Archivos_Compartidos(
        $_SESSION['usuario']['usuario_usuario'],
        $usuario_seleccionado
    );
    
    foreach($archivosCompartidos as $archivo) {
        $esRemitente = ($archivo['sender_usuario'] === $usuario_logeado);
        $claseMsg = $esRemitente ? 'msg_derecha' : 'msg_izquierda';
        
        echo '<div class="'.$claseMsg.' msg_archivo">';
        echo '<div class="contenido_archivo">';
        echo '<a href="archivos.php?action=descargar&id='.$archivo['id'].'" class="enlace_archivo">';
        echo '<i class="'.obtenerIconoArchivo($archivo['sender_nombre_original']).'"></i>';
        echo '<div>';
        echo '<strong>'.htmlspecialchars($archivo['sender_nombre_original']).'</strong>';
        echo '<small>';
        echo formatearTamanio($archivo['tamano_archivo']).' • ';
        echo date('d M H:i', strtotime($archivo['fecha_subida']));
        echo '</small>';
        echo '</div>';
        echo '</a>';
        echo '</div>';
        echo '<span class="time">';
        echo $esRemitente ? 'Tú' : htmlspecialchars($archivo['sender_usuario']);
        echo '</span>';
        echo '</div>';
    }
    
    echo '</div>';
}