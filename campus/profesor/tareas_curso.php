<?php
session_start();

// Inicializar variables
$nombre_curso = "";
$estado_curso = "";
$lista_tareas = "";

// Verificar si hay una sesión activa
if(isset($_SESSION['id_usuario'])) {
    // Obtener el ID del usuario
    $id_usuario = $_SESSION['id_usuario'];

    // Consultar la información del usuario desde la base de datos
    include('../php/conexion.php');

    // Consultar el nombre de usuario
    $consulta_usuario = "SELECT nombre FROM usuarios WHERE id_usuario = '$id_usuario'";
    $resultado_usuario = $conexion->query($consulta_usuario);

    if ($resultado_usuario->num_rows == 1) {
        $fila_usuario = $resultado_usuario->fetch_assoc();
        $nombre_usuario = $fila_usuario['nombre'];
    } else {
        // Si no se encuentra el usuario, asignar un valor predeterminado
        $nombre_usuario = "Usuario";
    }

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

            // Consulta las tareas asociadas al curso
            $consulta_tareas = "SELECT id_tarea, titulo, fecha_creacion FROM tareas_curso WHERE id_curso = '$id_curso'";
            $resultado_tareas = $conexion->query($consulta_tareas);
            
            // Verificar si hay tareas asociadas al curso
            if ($resultado_tareas->num_rows > 0) {
                // Construir la lista de tareas con enlaces a los detalles de cada tarea
                $lista_tareas = '<ul>';
                while ($fila_tarea = $resultado_tareas->fetch_assoc()) {
                    $id_tarea = $fila_tarea['id_tarea'];
                    $titulo_tarea = $fila_tarea['titulo'];
                    $fecha_creacion = $fila_tarea['fecha_creacion'];
                    $lista_tareas .= "<li><a href='detalle_tarea.php?id_tarea=$id_tarea'>$fecha_creacion - $titulo_tarea</a></li>";
                }
                $lista_tareas .= '</ul>';
            } else {
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
                        <!-- Enlace al perfil del alumno -->
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
                <h2><?php echo $nombre_curso; ?></h2>
                <hr class="container">
                <h5>Actividades recientes</h5>
            </div>
        </div>
    </section>

    <!-- Contenido de la página -->
    <div class="container">
        <div class="seccion">
            <h2>Detalles del Curso: <?php echo $nombre_curso; ?></h2>
            <p><strong>Estado:</strong> <?php echo $estado_curso; ?></p>
            <hr>
            <h4>Tareas:</h4>
            <?php echo $lista_tareas; ?>
            <hr>
            <!-- Botón para añadir nueva tarea -->
            <a href="agregar_tarea.php?id_curso=<?php echo $id_curso; ?>" class="btn btn-primary">Agregar Tarea</a>
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
