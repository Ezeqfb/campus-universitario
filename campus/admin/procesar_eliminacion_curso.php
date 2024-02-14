<?php
include('../php/conexion.php'); // Incluye el archivo de conexión

$id_curso = $_GET['id'];

$sql = "DELETE FROM cursos WHERE id_curso = $id_curso";

if ($conexion->query($sql) === TRUE) {
    $mensaje = "Curso eliminado exitosamente";
    $mostrarMensaje = true;
    header("Location: admin_cursos.php?mensaje=" . urlencode($mensaje) . "&mostrarMensaje=" . ($mostrarMensaje ? '1' : '0'));
    // Redirige a la página de lista de cursos y pasa el valor de la variable mensaje y mostrarMensaje
    exit;
} else {
    echo "Error al eliminar el curso: " . $conexion->error;
}

$conexion->close();
?>