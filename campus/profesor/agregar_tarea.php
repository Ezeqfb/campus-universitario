<?php
session_start();

// Declarar una variable para almacenar los detalles del curso
$detalles_curso_html = '';

// Verificar si hay una sesión activa
if (isset($_SESSION['id_usuario'])) {
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
        if ($rol_usuario != "profesor") {
            // Si no es un alumno, redirigir a la página de inicio de sesión
            header("Location: ../login.php");
            exit();
        }

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
    <title>Agregar Tarea</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/estilos.css">
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
                <h2>Administrar tareas</h2>
                <hr class="container">
                <h5>Agregar nueva tarea</h5>
            </div>
        </div>
    </section>

    <!-- Contenido -->
    <div class="container">
        <div class="seccion">
            <h2>Agregar Nueva Tarea</h2>
            <?php
            // Incluir el archivo de conexión a la base de datos
            include('../php/conexion.php');

            // Obtener el ID del curso de la URL
            $id_curso = isset($_GET['id_curso']) ? $_GET['id_curso'] : '';
            echo "ID del curso: " . $id_curso;

            // Verificar si el ID del curso está presente
            if (!empty($id_curso)) {
                // Verificar si se ha enviado el formulario
                if (isset($_POST['submit'])) {
                    // Obtener los datos del formulario
                    $titulo = isset($_POST['titulo']) ? $_POST['titulo'] : '';
                    $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
                    $fecha_creacion = date('Y-m-d');

                    // Verificar si se ha adjuntado un archivo
                    if ($_FILES['archivo_adjunto']['name']) {
                        $nombre_archivo = $_FILES['archivo_adjunto']['name'];
                        $ruta_temporal = $_FILES['archivo_adjunto']['tmp_name'];

                        // Mover el archivo a una ubicación específica en el servidor
                        $ruta_destino = '../media/docs/' . $nombre_archivo; // Directorio donde guardar los archivos
                        move_uploaded_file($ruta_temporal, $ruta_destino);
                    } else {
                        $ruta_destino = ''; // Si no se adjunta ningún archivo, la ruta será vacía
                    }

                    // Insertar la tarea en la base de datos
                    $sql_insert = "INSERT INTO tareas_curso (id_curso, titulo, descripcion, fecha_creacion, archivo_adjunto) VALUES (?, ?, ?, ?, ?)";
                    $stmt_insert = $conexion->prepare($sql_insert);
                    $stmt_insert->bind_param('issss', $id_curso, $titulo, $descripcion, $fecha_creacion, $ruta_destino);
                    if ($stmt_insert->execute()) {
                        echo "<div class='alert alert-success' role='alert'>Tarea agregada exitosamente</div>";
                    } else {
                        echo "<div class='alert alert-danger' role='alert'>Error al agregar la tarea: " . $stmt_insert->error . "</div>";
                    }
                }
            } else {
                // Si el ID del curso no está presente en la URL
                echo "<div class='alert alert-danger' role='alert'>ID del curso no proporcionado</div>";
            }

            // Cerrar la conexión
            $conexion->close();
            ?>

            <!-- Formulario para agregar una nueva tarea -->
            <form action="agregar_tarea.php?id_curso=<?php echo $id_curso; ?>" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="titulo">Título:</label>
                    <input type="text" class="form-control" id="titulo" name="titulo" required>
                </div>
                <div class="form-group">
                    <label for="descripcion">Descripción:</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                </div>
                <br>
                <div class="form-group">
                    <label for="archivo_adjunto">Adjuntar PDF:</label>
                    <input type="file" class="form-control-file" id="archivo_adjunto" name="archivo_adjunto">
                </div>
                <input type="hidden" name="id_curso" value="<?php echo $id_curso; ?>">
                <br>
                <button type="submit" name="submit" class="btn btn-primary">Agregar Tarea</button>
                <a class='btn btn-primary' href='pagina_profesor.php'>Cancelar</a>
            </form>
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
