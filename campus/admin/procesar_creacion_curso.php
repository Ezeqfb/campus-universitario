<?php
include('../php/conexion.php'); // Incluye el archivo de conexión

$nombre = $_POST['nombre'];
$profesor = $_POST['profesor'];

$sql = "INSERT INTO cursos (nombre, id_profesor) VALUES ('$nombre', '$profesor')";

if ($conexion->query($sql) === TRUE) {
    $mensaje = "El curso se creó con éxito.";
    $mostrarMensaje = true;
    header("Location: admin_cursos.php?mensaje=" . urlencode($mensaje) . "&mostrarMensaje=" . ($mostrarMensaje ? '1' : '0'));
    // Redirige a la página de lista de cursos y pasa el valor de la variable mensaje y mostrarMensaje
    exit;
} else {
    echo "Error al crear el curso: " . $conexion->error;
}

$conexion->close();
?>