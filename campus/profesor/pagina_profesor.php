<?php
session_start();

// Verificar si hay una sesión activa
if(isset($_SESSION['id_usuario'])) {
    // Obtener el ID del usuario
    $id_usuario = $_SESSION['id_usuario'];

    // Consultar la información del usuario desde la base de datos
    include('../php/conexion.php');

    $consulta_usuario = "SELECT * FROM usuarios WHERE id_usuario = '$id_usuario'";
    $resultado_usuario = $conexion->query($consulta_usuario);

    if ($resultado_usuario->num_rows == 1) {
        $fila_usuario = $resultado_usuario->fetch_assoc();
        $nombre_usuario = $fila_usuario['nombre'];
        $rol_usuario = $fila_usuario['rol']; // Obtener el rol del usuario

        // Verificar la jerarquía antes de permitir el acceso
        if ($rol_usuario !== "profesor") {
            // Si no es un profesor, redirigir a la página de inicio de sesión
            header("Location: ../login.php");
            exit();
        }

        // Obtener los cursos asignados al profesor
        $consulta_cursos = "SELECT * FROM cursos WHERE id_profesor = '$id_usuario'";
        $resultado_cursos = $conexion->query($consulta_cursos);

    } else {
        // En caso de error al obtener la información del usuario
        $nombre_usuario = "Usuario";
    }

    $conexion->close();
} else {
    // Si no hay una sesión activa, redirigir a la página de inicio de sesión
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/estilos.css">
    <title>Campus - Cursos</title>
</head>
<body>
    <!-- Header para el profesor -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <!-- Nombre del sitio o logo -->
                <a class="navbar-brand" href="#">Nombre del Sitio</a>

                <!-- Botón para dispositivos móviles -->
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Menú de navegación -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto">
                        <!-- Mostrar el nombre del usuario -->
                        <li class="nav-item">
                            <span class="navbar-text">
                                ¡Hola, <?php echo $nombre_usuario; ?>!
                            </span>
                        </li>

                        <!-- Enlace al perfil del profesor -->
                        <li class="nav-item">
                            <a class="nav-link" href="perfil_profesor.php">Perfil</a>
                        </li>

                        <!-- Botón para cerrar sesión -->
                        <li class="nav-item">
                            <form method="post" action="../php/cerrar_sesion.php">
                                <button type="submit" name="cerrar_sesion" class="nav-link btn btn-link">Cerrar Sesión</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Contenido de la página -->
    <div class="container">
        <h2>Cursos Asignados</h2>

        <?php
        if ($resultado_cursos->num_rows > 0) {
            while ($fila_curso = $resultado_cursos->fetch_assoc()) {
                echo '<a href="tareas_curso.php?id_curso=' . $fila_curso['id_curso'] . '" class="curso-caja">';
                echo '<div>';
                echo '<h3>' . $fila_curso['nombre'] . '</h3>';
                echo '<p>Estado: ' . $fila_curso['estado'] . '</p>';
                // Agrega más detalles según sea necesario
                echo '</div>';
                echo '</a>';
                echo '<hr>';
            }
        } else {
            echo '<p>No hay cursos asignados.</p>';
        }
        ?>
    </div>

    <!-- Scripts de Bootstrap y otros scripts necesarios -->
    <script src="../js/jquery.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
</body>
</html>
