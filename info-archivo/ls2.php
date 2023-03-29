<?php
function agregarDetallesDeArchivos($ruta, $xml, $recursivo = false) {

    // Obtener lista de archivos y carpetas
    $archivos = scandir($ruta);

    // Recorrer lista de archivos y carpetas
    foreach ($archivos as $archivo_actual) {

        // Comprobar si el elemento es un archivo
        if (is_file($ruta . "/" . $archivo_actual)) {
            // Obtener detalles del archivo
            $tamaño = filesize($ruta . "/" . $archivo_actual);
            $fecha = date("d/m/Y H:i:s", filemtime($ruta . "/" . $archivo_actual));
            $tipo = mime_content_type($ruta . "/" . $archivo_actual);

            // Crear elemento archivo y añadir atributos
            $elemento_archivo = $xml->addChild('archivo');
            $elemento_archivo->addAttribute('nombre', $archivo_actual);
            $elemento_archivo->addAttribute('tamaño', $tamaño);
            $elemento_archivo->addAttribute('fecha_modificacion', $fecha);
            $elemento_archivo->addAttribute('tipo', $tipo);
        }
        // Comprobar si el elemento es una subcarpeta y no es . o ..
        elseif (is_dir($ruta . "/" . $archivo_actual) && $archivo_actual != "." && $archivo_actual != "..") {
            // Crear elemento subcarpeta y añadir atributos
            $elemento_subcarpeta = $xml->addChild('subcarpeta');
            $elemento_subcarpeta->addAttribute('nombre', $archivo_actual);

            if ($recursivo) {
                // Llamar de manera recursiva a scandir para obtener detalles de los archivos de la subcarpeta
                agregarDetallesDeArchivos($ruta . "/" . $archivo_actual, $elemento_subcarpeta, $recursivo);
            }
        }
    }
}
$ruta = $_POST['ruta'];

// Crear objeto SimpleXMLElement
$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><archivos></archivos>');

// Comprobar si la ruta es un directorio válido
if (is_dir($ruta)) {
    agregarDetallesDeArchivos($ruta, $xml, true); // Llamar a la función con la opción recursiva activada
}

// Imprimir el XML resultante
header('Content-type: text/xml');
echo $xml->asXML();

// Generar enlace de descarga del archivo XML
$file_name = 'archivo.xml';
header("Content-Disposition: attachment; filename=\"$file_name\"");
header("Content-Type: application/xml"); 
exit;

?>
