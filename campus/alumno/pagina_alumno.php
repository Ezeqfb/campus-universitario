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
        if ($rol_usuario === "alumno") {
            // Obtener el ID del curso asignado al alumno desde la tabla inscripciones_curso
            $sqlInscripcion = "SELECT id_curso FROM inscripciones_curso WHERE id_alumno = '$id_usuario'";
            $resultadoInscripcion = $conexion->query($sqlInscripcion);

            if ($resultadoInscripcion->num_rows > 0) {
                // Iterar a través de los cursos inscritos por el alumno
                while ($filaInscripcion = $resultadoInscripcion->fetch_assoc()) {
                    // Obtener el ID del curso de la inscripción actual
                    $id_curso = $filaInscripcion['id_curso'];
                
                    // Consultar la información del curso desde la tabla cursos utilizando el ID obtenido
                    $sqlCurso = "SELECT * FROM cursos WHERE id_curso = '$id_curso'";
                    $resultadoCurso = $conexion->query($sqlCurso);
                
                    // Si se encuentra un curso, se muestran sus detalles
                    if ($resultadoCurso->num_rows == 1) {
                        $filaCurso = $resultadoCurso->fetch_assoc();
                        // Construir la cadena HTML con los detalles del curso
                        // Aplicar la clase CSS 'curso-caja' al enlace
                        $detalles_curso_html .= "<a href='tareas_curso.php?id_curso=$id_curso' class='curso-caja'>";
                        $detalles_curso_html .= "<div class='curso'>";
                        $detalles_curso_html .= "<img id='foto_curso' src='../media/img/b_curso.jpg'>";
                        $detalles_curso_html .= "<br><br>";
                        $detalles_curso_html .= "<h4><strong>Nombre del Curso:</strong> " . $filaCurso['nombre'] . "</h4>";
                        $detalles_curso_html .= "<h6><strong>Profesor:</strong> " . obtenerNombreProfesor($filaCurso['id_profesor'], $conexion) . "</h6>";
                        $detalles_curso_html .= "</div>";
                        $detalles_curso_html .= "</a>";
                        $detalles_curso_html .= "<hr class='container'>";
                    } else {

                        //Al eliminar un curso desde la base de datos, queda en la inscripción, por lo cual puede dar este error. De momento, queda comentado.
                        //echo "Error al obtener la información del curso.";
                    }
                }
            } else {
                echo "No estás inscrito en ningún curso.";
            }
        } else {
            // Si no es un alumno, redirigir a la página de inicio de sesión
            //header("Location: ../login.php");
            //exit();
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

// Función para obtener el nombre del profesor por su ID
function obtenerNombreProfesor($idProfesor, $conexion)
{
    $sql = "SELECT nombre FROM usuarios WHERE id_usuario = $idProfesor";
    $resultado = $conexion->query($sql);

    if ($resultado) {
        $fila = $resultado->fetch_assoc();
        return $fila['nombre'];
    } else {
        return "Error al obtener el nombre del profesor";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/estilos.css">
    <title>Campus - Curso</title>
</head>
<body id="alumno-body">

    <!-- Header para el alumno -->
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
                            <a class="nav-link" href="../index.php">PÁGINA PRINCIPAL</a>
                        </li>
                        <!-- Enlace al perfil del alumno -->
                        <li class="nav-item">
                            <a class="nav-link" href="pagina_alumno.php">INICIO</a>
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
                <h5>Área Personal</h5>
            </div>
        </div>
    </section>

    <!-- Cursos Section -->
    <section>
    <div class="container">
        <div class="seccion">
            <!-- Muestra los detalles del curso -->
            <h3>Cursos</h3>
            <hr class="container">
            <h5>Cursos en los que te encuentras inscripto</h5>
                <?php echo $detalles_curso_html; ?>
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

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
