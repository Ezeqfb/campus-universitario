<?php
include('../php/conexion.php'); // Incluye el archivo de conexión

$id_curso = isset($_POST['id_curso']) ? $_POST['id_curso'] : '';
$nombre = $_POST['nombre'];

$sql = "UPDATE cursos SET nombre = '$nombre' WHERE id_curso = $id_curso";

if ($conexion->query($sql) === TRUE) {
    $mensaje = "Se ha modificado con éxito.";
    $mostrarMensaje = true;
    header("Location: admin_cursos.php?mensaje=" . urlencode($mensaje) . "&mostrarMensaje=" . ($mostrarMensaje ? '1' : '0'));
    // Redirige a la página de lista de cursos y pasa el valor de la variable mensaje y mostrarMensaje
    exit;
} else {
    echo "Error al guardar los cambios: " . $conexion->error;
}

$conexion->close();
?>