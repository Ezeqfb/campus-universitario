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
                        $detalles_curso_html .= "<a href='pagina_curso.php?id_curso=$id_curso' class='curso-caja'>";
                        $detalles_curso_html .= "<div class='curso'>";
                        $detalles_curso_html .= "<h3>ID del Curso: " . $id_curso . "</h3>";
                        $detalles_curso_html .= "<p><strong>Nombre del Curso:</strong> " . $filaCurso['nombre'] . "</p>";
                        $detalles_curso_html .= "<p><strong>Profesor:</strong> " . obtenerNombreProfesor($filaCurso['id_profesor'], $conexion) . "</p>";
                        $detalles_curso_html .= "</div>";
                        $detalles_curso_html .= "</a>";
                        $detalles_curso_html .= "<hr>";
                    } else {
                        echo "Error al obtener la información del curso.";
                    }
                }
            } else {
                echo "No estás inscrito en ningún curso.";
            }
        } else {
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
<body>
    <!-- Header para el alumno -->
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

                        <!-- Enlace al perfil del alumno -->
                        <li class="nav-item">
                            <a class="nav-link" href="perfil_alumno.php">Perfil</a>
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

    <!-- Cursos -->
    <div class="container">
        <!-- Muestra los detalles del curso -->
        <h2>Detalles del Curso</h2>
            <?php echo $detalles_curso_html; ?>
    </div>

    <!-- Scripts de Bootstrap y otros scripts necesarios -->
    <script src="../js/jquery.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
</body>
</html>
