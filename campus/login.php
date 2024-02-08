<?php
session_start();

// Verificar si ya hay una sesión activa
if (isset($_SESSION['id_usuario'])) {
    // Si ya hay una sesión activa, redirige al index.php
    header("Location: alumno/pagina_alumno.php");
    exit(); // Asegura que el script se detenga después de la redirección
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include('php/conexion.php'); // Incluye el archivo de conexión

    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];

    // Consulta para obtener el rol del usuario
    $consulta = "SELECT id_usuario, contrasena, rol FROM usuarios WHERE nombre = '$usuario'";
    $resultado = $conexion->query($consulta);

    if ($resultado->num_rows == 1) {
        $fila = $resultado->fetch_assoc();

        // Verifica la contraseña
        if (password_verify($contrasena, $fila['contrasena'])) {
            $_SESSION['id_usuario'] = $fila['id_usuario'];
            $_SESSION['rol'] = $fila['rol'];

            // Redirige al usuario según su rol
            if ($fila['rol'] === "alumno") {
                header("Location: alumno/pagina_alumno.php");
            } elseif ($fila['rol'] === "profesor") {
                header("Location: profesor/pagina_profesor.php");
            } elseif ($fila['rol'] === "administrador") {
                header("Location: admin/pagina_administrador.php");
            } else {
                // Si el rol no coincide con ninguno de los anteriores, muestra un mensaje de acceso no autorizado.
                echo "Acceso no autorizado.";
            }
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {    
        echo "Usuario no encontrado.";
    }

    $conexion->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/estilos.css">

    <title>Iniciar Sesión</title>
</head>
<body>
    <h2>Iniciar Sesión</h2>
    <form action="" method="post">
        <label for="usuario">Usuario:</label>
        <input type="text" name="usuario" required><br>

        <label for="contrasena">Contraseña:</label>
        <input type="password" name="contrasena" required><br>

        <input type="submit" value="Iniciar Sesión">
        <p>¿Has olvidado tu contraseña? <a href="registro.php">Registrarse.</a></p>
    </form>
</body>
</html>