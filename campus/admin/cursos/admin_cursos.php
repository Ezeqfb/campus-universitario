<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/estilos.css">
    <title>Administrar Cursos</title>
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

    <h1>Hola admin!</h1>

    <?php
        // Incluye el archivo de conexión a la base de datos
        include('../../php/conexion.php');
        // Contar profesores
        $sqlProfesores = "SELECT COUNT(*) as total_profesores FROM usuarios WHERE rol = 'profesor'";
        $resultadoProfesores = $conexion->query($sqlProfesores);
        $datosProfesores = $resultadoProfesores->fetch_assoc();
        $totalProfesores = $datosProfesores['total_profesores'];
    ?>

    <!-- CRUD CURSOS -->

    <h2>Administrar Cursos</h2>

    <h2>Crear Curso</h2>
    <form action="procesar_creacion_curso.php" method="post">
        <label for="nombre">Nombre del Curso:</label>
        <input type="text" name="nombre" required><br>
        <label for "estado">Estado:</label>
        <select name="estado">
            <option value="activo">Activo</option>
            <option value="inactivo">Inactivo</option>
        </select><br>
        <label for="profesor">Profesor:</label>
        <select name="profesor">
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
        <br>
        <input type="submit" value="Crear Curso">
    </form>

    <br>
    <br>

    <h2>Editar cursos</h2>
    <hr>
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
                    echo "<h3>ID del Curso: " . $fila['id_curso'] . "</h3>";
                    echo "<p><strong>Nombre del Curso:</strong> " . $fila['nombre'] . "</p>";
                    echo "<p><strong>Profesor:</strong> " . obtenerNombreProfesor($fila['id_profesor'], $conexion) . "</p>";
                    echo "<p><a href='editar_curso.php?id=" . $fila['id_curso'] . "'>Modificar</a> | <a href='procesar_eliminacion_curso.php?id=" . $fila['id_curso'] . "' class='eliminar-link'>Eliminar</a> | <a href='agregar_alumnos.php?id=" . $fila['id_curso'] . "'>Agregar Alumnos</a></p>";
                    echo "<hr>";
                }
            } else {
                echo "No hay cursos.";
            }
        } else {
            echo "Error en la consulta: " . mysqli_error($conexion);
        }

        $conexion->close();
    ?>
    <script src="../confirmacion_eliminar.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var mensaje = "<?php echo isset($_GET['mensaje']) ? $_GET['mensaje'] : ''; ?>";
            var mostrarMensaje = "<?php echo isset($_GET['mostrarMensaje']) ? $_GET['mostrarMensaje'] : ''; ?>";

            if (mostrarMensaje === '1') {
                alert(mensaje);
            }
        });
    </script>
</body>
</html>