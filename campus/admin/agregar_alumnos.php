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
            // Si no es un administrador, redirigir a la página de inicio de sesión
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
    <title>Añadir alumnos</title>
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
                <h2>Administración de cursos</h2>
                <hr class="container">
                <h5>Agregar alumnos</h5>
            </div>
        </div>
    </section>

    <div class="container">
        <div class="seccion">
            <h3>Agregar Alumnos al Curso</h3>
            <hr class="container">
            <?php
            include('../php/conexion.php');

            // Validar la presencia y validez del parámetro 'id'
            if(isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
                $id_curso = $_GET['id'];

                echo "<h5>Curso ID :".$id_curso."</h5>";

                // Preparar la consulta para obtener la lista de alumnos
                $sql = "SELECT id_usuario, nombre FROM usuarios WHERE rol = 'alumno'";
                $resultadoAlumnos = $conexion->query($sql);

                // Obtener los alumnos ya inscritos en el curso
                $sql_inscritos = "SELECT id_alumno FROM inscripciones_curso WHERE id_curso = ?";
                $stmt = $conexion->prepare($sql_inscritos);
                $stmt->bind_param('i', $id_curso);
                $stmt->execute();
                $resultadoInscritos = $stmt->get_result();
                $inscritos = array();
                while ($fila = $resultadoInscritos->fetch_assoc()) {
                    $inscritos[] = $fila['id_alumno'];
                }

                // Mostrar el formulario para agregar alumnos
                if ($resultadoAlumnos->num_rows > 0) {
                    echo "<form action='' method='post'>";
                    echo "<input type='hidden' name='id_curso' value='".$id_curso."'>";
                    echo "<br>";
                    foreach ($resultadoAlumnos as $alumno) {
                        $checked = in_array($alumno['id_usuario'], $inscritos) ? 'checked disabled' : ''; // Deshabilitar el checkbox si el alumno ya está inscrito
                        echo "<div class='form-check'>";
                        echo "<input class='form-check-input' type='checkbox' name='alumnos[]' value='".$alumno['id_usuario']."' ".$checked.">";
                        echo "<label class='form-check-label' for='defaultCheck1'>".$alumno['nombre']."</label>";
                        echo "</div>";
                    }
                    echo "<input class='btn btn-primary' type='submit' name='submit' value='Agregar Alumnos'>";
                    echo "<a class='btn btn-primary' href='admin_cursos.php'>Regresar a la lista de cursos</a>";
                    echo "</form>";
                } else {
                    echo "<div class='alert alert-warning' role='alert'>No hay alumnos disponibles para agregar.</div>";
                }

                // Procesar el formulario cuando se envíe
                if (isset($_POST['submit'])) {
                    if (isset($_POST['alumnos'])) {
                        $alumnos_seleccionados = $_POST['alumnos'];
                        foreach ($alumnos_seleccionados as $id_alumno) {
                            $sql_insert = "INSERT INTO inscripciones_curso (id_curso, id_alumno) VALUES (?, ?)";
                            $stmt = $conexion->prepare($sql_insert);
                            $stmt->bind_param("ii", $id_curso, $id_alumno);
                            if ($stmt->execute()) {
                                echo "<div class='alert alert-success' role='alert'>Los alumnos se agregaron al curso con éxito.</div>";
                            } else {
                                echo "<div class='alert alert-danger' role='alert'>Error al agregar los alumnos al curso: " . $stmt->error . "</div>";
                            }
                            $stmt->close();
                        }
                    } else {
                        echo "<div class='alert alert-warning' role='alert'>No se seleccionaron alumnos para agregar al curso.</div>";
                    }
                }

            } else {
                // Mostrar mensaje de error si el parámetro 'id' es inválido
                echo "<div class='alert alert-danger' role='alert'>Error: El parámetro 'id' es inválido.</div>";
            }
            $conexion->close();
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
