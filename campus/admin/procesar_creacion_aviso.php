<?php
// Incluye el archivo de conexión a la base de datos
include('../php/conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtiene los datos del formulario
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];

    // Inserta el aviso en la base de datos
    $sql = "INSERT INTO avisos (titulo, descripcion) VALUES ('$titulo', '$descripcion')";

    if ($conexion->query($sql) === TRUE) {
        $mensaje = "El aviso se creó con éxito.";
        $mostrarMensaje = true;
        header("Location: admin_avisos.php?mensaje=" . urlencode($mensaje) . "&mostrarMensaje=" . ($mostrarMensaje ? '1' : '0'));
        // Redirige a la página de lista de cursos y pasa el valor de la variable mensaje y mostrarMensaje
        exit;
    } else {
        echo "Error al crear el aviso: " . $conexion->error;
    }
}

$conexion->close();
?>