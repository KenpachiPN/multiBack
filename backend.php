<?php

header('Content-Type: application/json');
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Directorio donde se guardará el archivo en el servidor
$directorioDestino = __DIR__ . '/Multimedia/';

// Recibe los datos del formulario
$user = isset($_POST['usuario']) ? trim(strtolower($_POST['usuario'])) : '';
$passwd = isset($_POST['contrasena']) ? trim($_POST['contrasena']) : '';
$deposito = isset($_POST['deposito']) ? $_POST['deposito'] : '';
$recaptchaResponse = isset($_POST['recaptchaResponse']) ? $_POST['recaptchaResponse'] : '';
$multi = isset($_FILES['files']) ? $_FILES['files'] : null;


if (empty($user) || empty($passwd) || empty($deposito) || empty($recaptchaResponse) || $multi === null) {
    echo json_encode(['success' => false, 'message' => '¡Hay campos vacios!']);
    exit;
}
// Mueve el archivo a la ubicación
$nombreArchivo = $multi['name'];
$rutaDestino = $directorioDestino . $nombreArchivo;
$rutaTemporal = $multi['tmp_name'];
$estadoMulti = false;

if (move_uploaded_file($rutaTemporal, $rutaDestino)) {
    $estadoMulti = true;
} else {
    $estadoMulti = false;
}

echo json_encode(['estad' => $estadoMulti]);

if ($estadoMulti) {
    $formData = [
        'success' => true,
        'message' => 'Multimedia guardada éxitosamente',
        'data' => [
            'user' => $user,
            'pass' => $passwd,
            'deposito' => $deposito,
            'responCap' => $recaptchaResponse,
            'Multimedia' => [
                'status' => $estadoMulti,
                'ruta' => $rutaDestino
            ],
        ],

    ];
} else {
    $formData = [
        'success' => false,
        'message' => 'La multimedia no se guardó'
    ];
}

echo json_encode($formData);
exit;

?>