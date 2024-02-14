<?php
session_start();

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
        if ($rol_usuario != "administrador") {
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/estilos.css">
    <title>Administrar Cursos</title>
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
                        <!-- Enlace a la página principal -->
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">PÁGINA PRINCIPAL</a>
                        </li>
                        <!-- Enlace al perfil del alumno -->
                        <li class="nav-item">
                            <a class="nav-link" href="pagina_administrador.php">INICIO</a>
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
                <h2>¡Bienvenido <?php echo $rol_usuario; ?> <?php echo $nombre_usuario; ?>!</h2>
                <hr class="container">
                <h5>Administración de cursos</h5>
            </div>
        </div>
    </section>


    <!-- Contenido de la página -->
    <section>
        <div class="container">
            <div class="seccion">
                <?php
                    // Incluye el archivo de conexión a la base de datos
                    include('../php/conexion.php');
                    // Contar profesores
                    $sqlProfesores = "SELECT COUNT(*) as total_profesores FROM usuarios WHERE rol = 'profesor'";
                    $resultadoProfesores = $conexion->query($sqlProfesores);
                    $datosProfesores = $resultadoProfesores->fetch_assoc();
                    $totalProfesores = $datosProfesores['total_profesores'];
                ?>

                <!-- CRUD CURSOS -->

                <h3>Administrar Cursos</h2>
                <hr class="container">
                <h5>Crear Curso</h5>
                <?php
                // Verificar si se ha enviado el formulario de creación de curso
                if (isset($_POST['submit'])) {
                    $nombre = $_POST['nombre'];
                    $profesor = $_POST['profesor'];

                    // Tu código para la inserción del curso
                    $sql = "INSERT INTO cursos (nombre, id_profesor) VALUES ('$nombre', '$profesor')";

                    if ($conexion->query($sql) === TRUE) {
                        // Mensaje de éxito si la inserción se realizó correctamente
                        echo "<div class='alert alert-success' role='alert'>El curso se creó con éxito.</div>";
                    } else {
                        // Mensaje de error si ocurrió algún problema durante la inserción
                        echo "<div class='alert alert-danger' role='alert'>Error al crear el curso: " . $conexion->error . "</div>";
                    }
                }
                
                ?>

            
                <form action="" method="post">
                    <div class="form-group">
                        <label for="nombre">Nombre del Curso:</label>
                        <input class="form-control" type="text" name="nombre" required><br>
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado:</label>
                        <select class="form-select" name="estado">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                    <br>
                    <div class="form-group">
                        <label for="profesor">Profesor:</label>
                        <select class="form-select" name="profesor" required>
                            <?php
                            // Consulta para obtener la lista de profesores
                            $sqlProfesores = "SELECT id_usuario, nombre FROM usuarios WHERE rol = 'profesor'";
                            $resultadoProfesores = $conexion->query($sqlProfesores);

                            // Verifica si hay profesores disponibles
                            if ($resultadoProfesores->num_rows > 0) {
                                echo "<option value='' disabled>Elegir profesor..</option>";
                                // Itera a través de la lista de profesores y crea opciones en la lista desplegable
                                while ($filaProfesor = $resultadoProfesores->fetch_assoc()) {
                                    echo "<option value='" . $filaProfesor['id_usuario'] . "'>" . $filaProfesor['nombre'] . "</option>";
                                }
                            } else {
                                echo "<option value='' disabled>No hay profesores</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <br>
                    <input class="btn btn-primary" type="submit" name="submit" value="Crear Curso">
                </form>

                <br>
                <br>

                <h3>Editar cursos</h3>
                <hr class="container">
                <?php
                    // Consulta para obtener la lista de todos los cursos
                    $sqlCursos = "SELECT * FROM cursos";
                    $resultado = $conexion->query($sqlCursos);

                    function obtenerNombreProfesor($idProfesor, $conexion) {
                        // Consulta para obtener el nombre del profesor por su ID
                        $sql = "SELECT nombre FROM usuarios WHERE id_usuario = $idProfesor";
                        $resultado = $conexion->query($sql);
                    
                        if ($resultado) {
                            $fila = $resultado->fetch_assoc();
                            return $fila['nombre'];
                        } else {
                            return "Error al obtener el nombre del profesor";
                        }
                    }

                    if ($resultado) {
                        if ($resultado->num_rows > 0) {
                            // Itera a través de la lista de cursos
                            while ($fila = $resultado->fetch_assoc()) {
                                echo "<h4>ID del Curso: " . $fila['id_curso'] . "</h4>";
                                echo "<p><strong>Nombre del Curso:</strong> " . $fila['nombre'] . "</p>";
                                echo "<p><strong>Profesor:</strong> " . obtenerNombreProfesor($fila['id_profesor'], $conexion) . "</p>";
                                echo "<p><a class='btn btn-primary' href='editar_curso.php?id=" . $fila['id_curso'] . "'>Modificar</a>   <a class='btn btn-primary' href='eliminar_curso.php?id=" . $fila['id_curso'] . "' class='eliminar-link'>Eliminar</a>   <a class='btn btn-primary' href='agregar_alumnos.php?id=" . $fila['id_curso'] . "'>Agregar Alumnos</a></p>";
                                echo "<hr class='container'>";
                            }
                        } else {
                            echo "No hay cursos.";
                        }
                    } else {
                        echo "Error en la consulta: " . mysqli_error($conexion);
                    }

                    $conexion->close();
                ?>
            </div>
        </div>
    </section>

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