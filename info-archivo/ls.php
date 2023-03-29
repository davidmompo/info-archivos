<?php
function agregarDetallesDeArchivos($ruta, &$json, $recursivo = false) {
    // Obtener lista de archivos y carpetas
    $archivos = scandir($ruta);

    // Recorrer lista de archivos y carpetas
    foreach ($archivos as $archivo_actual) {
        // Comprobar si el elemento es un archivo
        if (is_file($ruta . "/" . $archivo_actual)) {
            // Obtener detalles del archivo
            $tamano = filesize($ruta . "/" . $archivo_actual);
            $fecha = date("d/m/Y H:i:s", filemtime($ruta . "/" . $archivo_actual));
            $tipo = mime_content_type($ruta . "/" . $archivo_actual);

            // Crear objeto para el archivo y añadir atributos
            $archivo = new stdClass();
            $archivo->nombre = $archivo_actual;
            $archivo->tamano = $tamano;
            $archivo->fecha_modificacion = $fecha;
            $archivo->tipo = $tipo;

            // Agregar objeto archivo al array JSON
            $json[] = $archivo;
        }

        // Comprobar si el elemento es una subcarpeta y no es . o ..
        elseif (is_dir($ruta . "/" . $archivo_actual) && $archivo_actual != "." && $archivo_actual != "..") {
            // Crear objeto para la subcarpeta y añadir atributos
            $subcarpeta = new stdClass();
            $subcarpeta->nombre = $archivo_actual;

            if ($recursivo) {
                // Inicializar array JSON para los archivos de la subcarpeta
                $subcarpeta->archivos = array();

                // Llamar de manera recursiva a scandir para obtener detalles de los archivos de la subcarpeta
                agregarDetallesDeArchivos($ruta . "/" . $archivo_actual, $subcarpeta->archivos, $recursivo);
            }

            // Agregar objeto subcarpeta al array JSON
            $json[] = $subcarpeta;
        }
    }
}

$ruta = $_POST['ruta'];

// Inicializar array JSON
$json = array();

// Comprobar si la ruta es un directorio válido
if (is_dir($ruta)) {
    agregarDetallesDeArchivos($ruta, $json, true); // Llamar a la función con la opción recursiva activada
}

// Imprimir el JSON resultante
header('Content-Type: application/json');
echo json_encode($json);

// Generar enlace de descarga del archivo JSON
$file_name = 'archivo.json';
header("Content-Disposition: attachment; filename=\"$file_name\"");
header("Content-Type: application/json");
exit;

?>
