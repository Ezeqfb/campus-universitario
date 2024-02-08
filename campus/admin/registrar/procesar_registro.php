<?php
include('../../php/conexion.php'); // Incluye el archivo de conexión

// Recuperar los datos del formulario
$nombre = $_POST['nombre'];
$correo = $_POST['correo'];
$contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT); // Hash de la contraseña
$rol = $_POST['rol'];

// Insertar los datos en la base de datos
$sql = "INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES ('$nombre', '$correo', '$contrasena', '$rol')";

if ($conexion->query($sql) === TRUE) {
    echo "Registro exitoso";
} else {
    echo "Error en el registro: " . $conexion->error;
}

// Cerrar la conexión a la base de datos
$conexion->close();
?>