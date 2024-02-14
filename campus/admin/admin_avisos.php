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
        if ($rol_usuario !== "administrador") {
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/estilos.css">
    <title>Administrar avisos</title>
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
                            <a class="nav-link" href="../index.php">PÁGINA PRINCIPAL</a>
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
                <h5>Administración de avisos</h5>
            </div>
        </div>
    </section>

    <div class="container">
        <div class="seccion">
            <!-- CRUD CURSOS -->

            <h3>Administrar Avisos</h3>
            <hr class="container">

            <h5>Crear Aviso</h5>
            <form action="procesar_creacion_aviso.php" method="post">
                <label for="titulo">Título del Aviso:</label>
                <input class="form-control" type="text" name="titulo" required><br>
                <label for="descripcion">Descripción del Aviso:</label>
                <textarea class="form-control" name="descripcion" rows="4" required></textarea><br>
                <input class="btn btn-primary" type="submit" value="Crear Aviso">
            </form>

            <br>
            <br>

            <h3>Editar avisos</h3>
            <hr class="container">

            <?php
                include('../php/conexion.php');
                // Consulta para obtener la lista de todos los cursos
                $sqlAvisos = "SELECT * FROM avisos";
                $resultado = $conexion->query($sqlAvisos);

                if ($resultado) {
                    if ($resultado->num_rows > 0) {
                        // Itera a través de la lista de cursos
                        while ($fila = $resultado->fetch_assoc()) {
                            echo "<h4>Código de aviso: " . $fila['id_aviso'] . "</h4>";
                            echo "<p><strong>Nombre del Aviso:</strong> " . $fila['titulo'] . "</p>";
                            echo "<p><strong>fecha de publicación:</strong> " . $fila['fecha_publicacion'] . "</p>";
                            echo "<p><strong>descripción:</strong> " . $fila['descripcion'] . "</p>";
                            echo "<p><a class='btn btn-primary' href='editar_aviso.php?id=" . $fila['id_aviso'] . "'>Modificar</a>   <a class='btn btn-primary' href='eliminar_avisos.php?id=" . $fila['id_aviso'] . "' class='eliminar-link'>Eliminar</a></p>";
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