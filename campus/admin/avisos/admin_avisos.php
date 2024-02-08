<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/estilos.css">
    <title>Administrar avisos</title>
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
                            <form method="post" action="../../php/cerrar_sesion.php">
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

    <h2>Administrar Avisos</h2>

    <h2>Crear Aviso</h2>
    <form action="procesar_creacion_aviso.php" method="post">
        <label for="titulo">Título del Aviso:</label>
        <input type="text" name="titulo" required><br>
        <label for="descripcion">Descripción del Aviso:</label>
        <textarea name="descripcion" rows="4" required></textarea><br>
        <input type="submit" value="Crear Aviso">
    </form>

    <br>
    <br>

    <h2>Editar avisos</h2>
    <hr>
    <?php
        // Consulta para obtener la lista de todos los cursos
        $sqlAvisos = "SELECT * FROM avisos";
        $resultado = $conexion->query($sqlAvisos);

        if ($resultado) {
            if ($resultado->num_rows > 0) {
                // Itera a través de la lista de cursos
                while ($fila = $resultado->fetch_assoc()) {
                    echo "<h3>Código de aviso: " . $fila['id_aviso'] . "</h3>";
                    echo "<p><strong>Nombre del Aviso:</strong> " . $fila['titulo'] . "</p>";
                    echo "<p><strong>fecha de publicación:</strong> " . $fila['fecha_publicacion'] . "</p>";
                    echo "<p><strong>descripción:</strong> " . $fila['descripcion'] . "</p>";
                    echo "<p><a href='editar_aviso.php?id=" . $fila['id_aviso'] . "'>Modificar</a> | <a href='procesar_eliminacion_aviso.php?id=" . $fila['id_aviso'] . "' class='eliminar-link'>Eliminar</a></p>";
                    echo "<hr>";
                }
            } else {
                echo "No hay avisos.";
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