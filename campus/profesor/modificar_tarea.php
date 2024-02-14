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
            //header("Location: ../login.php");
            //exit();
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
    <title>Modificar Tarea</title>
</head>
<body id="index-body">

    <!-- Header para el admin -->
    <header class="bg-white">
        <nav class="navbar navbar-expand navbar-light container">
            <div class="container-fluid">
                <!-- Nombre del sitio o logo -->
                <img id="logo" src="../media/img/cudi1.png" alt="">

                <!-- Botón para dispositivos móviles 
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>-->

                <!-- Menú de navegación -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav" style="margin-left: auto;">
                        <!-- Nombre del usuario -->
                        <li class="nav-item">
                            <p class="nav-link">HOLA, <?php echo $nombre_usuario; ?></p>
                        </li>
                        <!-- Enlace a la página principal -->
                        <li class="nav-item">
                            <a class="nav-link" href="../index.php">PÁGINA PRINCIPAL</a>
                        </li>
                        <!-- Enlace al perfil del profesor -->
                        <li class="nav-item">
                            <a class="nav-link" href="pagina_profesor.php">INICIO</a>
                        </li>
                        <!-- Botón para cerrar sesión -->
                        <li class="nav-item">
                            <form method="post" action="../php/cerrar_sesion.php">
                                <button type="submit" name="cerrar_sesion" class="nav-link btn btn-link">CERRAR SESIÓN</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Home Section -->
    <section id="home">
        <div class="container">
            <div class="txt">
                <h2>Modificar Tarea</h2>
                <hr class="container">
                <h5>Editar los detalles de la tarea</h5>
            </div>
        </div>
    </section>

    <!-- Contenido de la página -->
    <div class="container">
        <div class="seccion">
            <?php
            // Verificar si se proporcionó un ID de tarea válido en la URL
            if (!empty($_GET['id_tarea'])) {
                // Obtener el ID de la tarea desde la URL
                $id_tarea = $_GET['id_tarea'];

                // Incluir el archivo de conexión a la base de datos
                include('../php/conexion.php');

                // Consultar los detalles de la tarea y el nombre del curso al que pertenece
                $sql = "SELECT t.*, c.nombre AS nombre_curso 
                        FROM tareas_curso t 
                        INNER JOIN cursos c ON t.id_curso = c.id_curso 
                        WHERE t.id_tarea = ?";
                $stmt = $conexion->prepare($sql);
                $stmt->bind_param('i', $id_tarea);
                $stmt->execute();
                $resultado = $stmt->get_result();

                // Verificar si se encontró la tarea
                if ($resultado->num_rows == 1) {
                    $fila = $resultado->fetch_assoc();
                    $nombre_curso = $fila['nombre_curso'];
                    // Otros detalles de la tarea
                    $titulo_tarea = $fila['titulo'];
                    $descripcion_tarea = $fila['descripcion'];
                    $fecha_creacion_tarea = $fila['fecha_creacion'];
                    $archivo_adjunto = $fila['archivo_adjunto'];

                    // Mostrar los detalles de la tarea y el formulario para modificar
                    echo "<h2>Detalles de la Tarea: $titulo_tarea</h2>";
                    echo "<p><strong>Curso:</strong> $nombre_curso</p>";
                    echo "<p><strong>Fecha de Creación:</strong> $fecha_creacion_tarea</p>";
                    echo "<p><strong>Descripción:</strong></p>";
                    echo "<p>$descripcion_tarea</p>";
                    echo "<h5>Editar detalles de la tarea:</h5>";
                    echo "<form action='' method='post'>";
                    echo "<label for='titulo'>Título:</label>";
                    echo "<input class='form-control' type='text' name='titulo' value='$titulo_tarea' required>";
                    echo "<br>";
                    echo "<label for='descripcion'>Descripción:</label>";
                    echo "<textarea class='form-control' name='descripcion' required>$descripcion_tarea</textarea>";
                    echo "<br>";
                    echo "<input class='btn btn-primary' type='submit' name='submit' value='Guardar Cambios'>";
                    echo "<a class='btn btn-primary' href='detalle_tarea.php?id_tarea=$id_tarea'>Cancelar</a>";
                    echo "</form>";

                    // Procesar la actualización de la tarea si se envió el formulario
                    if (isset($_POST['submit'])) {
                        $nuevo_titulo = $_POST['titulo'];
                        $nueva_descripcion = $_POST['descripcion'];
                        $sql_update = "UPDATE tareas_curso SET titulo = ?, descripcion = ? WHERE id_tarea = ?";
                        $stmt_update = $conexion->prepare($sql_update);
                        $stmt_update->bind_param('ssi', $nuevo_titulo, $nueva_descripcion, $id_tarea);
                        if ($stmt_update->execute()) {
                            echo "<div class='alert alert-success' role='alert'>Los detalles de la tarea se actualizaron correctamente.</div>";
                            // Redirigir a la página de detalles de la tarea después de actualizar
                            echo "<meta http-equiv='refresh' content='2; URL=detalle_tarea.php?id_tarea=$id_tarea'>";
                        } else {
                            echo "<div class='alert alert-danger' role='alert'>Error al actualizar los detalles de la tarea: " . $stmt_update->error . "</div>";
                        }
                        $stmt_update->close();
                    }
                } else {
                    echo "<p>No se encontró la tarea.</p>";
                }

                // Cerrar la conexión a la base de datos
                $conexion->close();
            } else {
                echo "<p>No se proporcionó un ID de tarea válido en la URL.</p>";
            }
            ?>
        </div>
    </div>

    <!-- Avisos Section -->
    <section>
        <div class="carrusel">
            <div class="container">
                <h3>Avisos</h3>
                <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        <?php
                        include('../php/conexion.php');
                        $sqlAvisos = "SELECT * FROM avisos";
                        $resultado = $conexion->query($sqlAvisos);
                        $contador = 0;
                        if ($resultado) {
                            if ($resultado->num_rows > 0) {
                                while ($fila = $resultado->fetch_assoc()) {
                                    // Agregar un indicador para cada aviso
                                    echo '<li data-target="#carouselExampleIndicators" data-slide-to="' . $contador . '" class="' . ($contador == 0 ? 'active' : '') . '"></li>';
                                    $contador++;
                                }
                            }
                        }
                        ?>
                    </ol>
                    <div class="carousel-inner">
                        <?php
                        $resultado = $conexion->query($sqlAvisos);
                        $contador = 0;
                        if ($resultado) {
                            if ($resultado->num_rows > 0) {
                                while ($fila = $resultado->fetch_assoc()) {
                                    // Agregar un slide para cada aviso
                                    echo '<div class="carousel-item ' . ($contador == 0 ? 'active' : '') . '">';
                                    echo '<h4><strong>Nombre del Aviso:</strong> ' . $fila['titulo'] . '</h4>';
                                    echo '<p><strong>fecha de publicación:</strong> ' . $fila['fecha_publicacion'] . '</p>';
                                    echo '<p><strong>descripción:</strong> ' . $fila['descripcion'] . '</p>';
                                    echo '</div>';
                                    $contador++;
                                }
                            }
                        }
                        ?>
                    </div>
                    <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!--footer-->
    <footer>
        <h4>Proyecto desarrollado por Fernando Bernal</h4>
    </footer>

    <!-- Scripts de Bootstrap y otros scripts necesarios -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
