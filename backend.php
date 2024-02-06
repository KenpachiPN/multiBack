<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Content-Type');

// Recibe los datos del formulario
$user = isset($_POST['usuario']) ? trim(strtolower($_POST['usuario'])) : '';
$passwd = isset($_POST['contrasena']) ? trim($_POST['contrasena']) : '';
$deposito = isset($_POST['deposito']) ? $_POST['deposito'] : '';
$recaptchaResponse = isset($_POST['recaptchaResponse']) ? $_POST['recaptchaResponse'] : '';
$multi = isset($_FILES['files']) ? $_FILES['files'] : null;
//Seteamos a la hora de nuestro pais
date_default_timezone_set('America/Bogota');
$fecha = date("Y-m-d H:i:s");

// Directorio donde se guardará el archivo en el servidor
$rutaRelativa = '/Multimedia/' . $deposito . '/';
// Directorio donde está el JSON
$rutaJson = __DIR__ . '/Multimedia/' . $deposito . '/' . $deposito . '.json';

if (empty($user) || empty($passwd) || empty($deposito) || empty($recaptchaResponse) || $multi === null) {
    $formDataErr = [
        'success' => false,
        'message' => 'Error interno',
        'data' => [
            'user' => $user,
            'pass' => $passwd,
            'deposito' => $deposito,
            'responCap' => $recaptchaResponse,
            'Multimedia' => $multi
        ],
    ];
    echo json_encode($formDataErr);
    exit;
}

// Obtener los datos del JSON
$fileJson = json_decode(file_get_contents($rutaJson), true);

// Obtener el último número utilizado
$contador = empty($fileJson) ? 1 : $fileJson[count($fileJson) - 1]['data']['Numero'];
$contador++;  // Incrementar el contador

// Crear un aray para almacenar el archivo
$files = [];

foreach ($_FILES['files']['name'] as $i => $files_name) {
    // Obtener la extensión del archivo
    $extension = pathinfo($files_name, PATHINFO_EXTENSION);
    // Adjuntar el nuevo nombre
    $nuevoNombre = $contador . '.' . $extension;
    $rutaDestino = __DIR__ . $rutaRelativa . basename($nuevoNombre);
    $rutaDestinoJson = $rutaJson;
    if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $rutaDestino)) {
        $files[] = [
            'name' => $files_name,
            'ruta' => $rutaRelativa . $nuevoNombre,
            'nuevo nombre' => $nuevoNombre,
        ];
        $formData = [
            'success' => true,
            'message' => 'Multimedia guardada éxitosamente',
            'data' => [
                'user' => $user,
                'pass' => $passwd,
                'deposito' => $deposito,
                'responCap' => $recaptchaResponse,
                'Multimedia' => $files,
                'Fecha subida' => $fecha
            ],
        ];
        // Actualizar el contador en el array
        $fileJson[] = ['data' => $formData['data']];
        $fileJson[count($fileJson) - 1]['data']['Numero'] = $contador;

        // Incrementar el contador
        $contador++;
        // Codificar el array completo en formato JSON
        $jsonData = json_encode($fileJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        // Guardar el array completo en el archivo JSON
        file_put_contents($rutaDestinoJson, $jsonData);
        //Mostrar la respuesta
        echo json_encode($formData);
    } else {
        $formData = [
            'success' => false,
            'message' => 'La multimedia no se guardó'
        ];
        echo json_encode($formData);
    }
}

exit;

?>