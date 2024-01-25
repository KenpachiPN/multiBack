<?php

header('Content-Type: application/json');

// Directorio donde se guardará el archivo en el servidor
$directorioDestino = '/MultimediaBack/Multimedia/';

// Recibe los datos del formulario
$user = isset($_POST['usuario']) ? trim(strtolower($_POST['usuario'])) : '';
$passwd = isset($_POST['contrasena']) ? trim($_POST['contrasena']) : '';
$deposito = isset($_POST['deposito']) ? $_POST['deposito'] : '';
$recaptchaResponse = isset($_POST['recaptchaResponse']) ? $_POST['recaptchaResponse'] : '';
$multi = isset($_FILES['multimedia']) ? $_FILES['multimedia'] : null;


// Verifica si se ha subido un archivo
if ($multi !== null && $multi['error'] == UPLOAD_ERR_OK) {
    // Obtiene la información del archivo
    $nombreArchivo = basename($multi['name']);
    $rutaTemporal = $multi['tmp_name'];

    // Mueve el archivo a la ubicación
    $rutaDestino = $directorioDestino . $nombreArchivo;
    if (move_uploaded_file($rutaTemporal, $rutaDestino)) {
        echo json_encode(['success' => true, 'message' => 'Multimedia enviada con éxito']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al mover el archivo']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No se ha seleccionado ningún archivo']);
}

exit;
?>
