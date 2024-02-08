<?php
$nombre_servidor = "localhost";
$usuario_db = "root";
$contraseña_db = "";
$nombre_db = "campus";

$conexion = mysqli_connect($nombre_servidor, $usuario_db, $contraseña_db, $nombre_db);

if (!$conexion) {
    die("Error al conectar con la base de datos: " . mysqli_connect_error());
}
?>