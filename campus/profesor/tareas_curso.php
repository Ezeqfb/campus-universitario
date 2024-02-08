<?php
session_start();

// Verificar si hay una sesión activa
if(isset($_SESSION['id_usuario'])) {
    // Obtener el ID del usuario
    $id_usuario = $_SESSION['id_usuario'];

    // Consultar la información del usuario desde la base de datos
    include('../php/conexion.php');

    // Verificar si se proporcionó un ID de curso válido en la URL
    if(isset($_GET['id_curso']) && is_numeric($_GET['id_curso'])) {
        $id_curso = $_GET['id_curso'];

        // Consultar la información del curso desde la base de datos
        $consulta_curso = "SELECT * FROM cursos WHERE id_curso = '$id_curso'";
        $resultado_curso = $conexion->query($consulta_curso);

        // Verificar si se encontró el curso
        if ($resultado_curso->num_rows == 1) {
            $fila_curso = $resultado_curso->fetch_assoc();
            $nombre_curso = $fila_curso['nombre'];
            $estado_curso = $fila_curso['estado'];
            // Aquí puedes agregar más detalles del curso según sea necesario

            // Consultar las tareas asociadas al curso desde la base de datos
            $consulta_tareas = "SELECT * FROM tareas_curso WHERE id_curso = '$id_curso'";
            $resultado_tareas = $conexion->query($consulta_tareas);

            // Verificar si hay tareas asociadas al curso
            if ($resultado_tareas->num_rows > 0) {
                // Si hay tareas, las mostramos
                $lista_tareas = '<ul>';
                while ($fila_tarea = $resultado_tareas->fetch_assoc()) {
                    $lista_tareas .= '<li>' . $fila_tarea['fecha_creacion'] . ' - ' . $fila_tarea['descripcion'] . '</li>';
                }
                $lista_tareas .= '</ul>';
            } else {
                // Si no hay tareas, mostramos un mensaje
                $lista_tareas = '<p>No hay tareas para este curso.</p>';
            }
        } else {
            // Si no se encontró el curso, mostramos un mensaje
            $nombre_curso = "Curso no encontrado";
            $estado_curso = "Desconocido";
            $lista_tareas = '<p>No hay información disponible para este curso.</p>';
        }
    } else {
        // Si no se proporcionó un ID de curso válido en la URL, mostramos un mensaje
        $nombre_curso = "Curso no especificado";
        $estado_curso = "Desconocido";
        $lista_tareas = '<p>No se ha especificado un curso.</p>';
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
    <title>Detalles del Curso</title>
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
        <h2>Detalles del Curso: <?php echo $nombre_curso; ?></h2>
        <p><strong>Estado:</strong> <?php echo $estado_curso; ?></p>
        <hr>
        <h3>Tareas:</h3>
        <?php echo $lista_tareas; ?>
        <hr>
        <!-- Botón para añadir nueva tarea -->
        <a href="agregar_tarea.php?id_curso=<?php echo $id_curso; ?>" class="btn btn-primary">Agregar Tarea</a>
    </div>

    <!-- Scripts de Bootstrap y otros scripts necesarios -->
    <script src="../js/jquery.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
</body>
</html>
