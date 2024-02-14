<?php
session_start();

// Declarar una variable para almacenar los detalles del aviso
$detalles_aviso_html = '';

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
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/estilos.css">
    <title>Editar aviso</title>
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
            <h2>Administración de avisos</h2>
            <hr class="container">
            <h5>Editar aviso</h5>
        </div>
    </div>
</section>

<!-- Contenido -->
<div class="container">
    <div class="seccion">
        <h3>Editar aviso</h3>
        <hr class="container">
        <?php
        include('../php/conexion.php');

        // Validar la presencia y validez del parámetro 'id'
        if(isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
            $id_aviso = $_GET['id'];
            echo "<h5>Aviso ID :".$id_aviso."</h5>";

            // Verificar si se ha enviado el formulario de confirmación de modificación
            if (isset($_POST['submit'])) {
                // Obtener los datos del formulario
                $nuevo_titulo = $_POST['titulo'];
                $nueva_descripcion = $_POST['descripcion'];
                
                // Preparar la consulta para actualizar el aviso
                $sql = "UPDATE avisos SET titulo = ?, descripcion = ? WHERE id_aviso = ?";
                $stmt = $conexion->prepare($sql);
                $stmt->bind_param("ssi", $nuevo_titulo, $nueva_descripcion, $id_aviso);

                // Ejecutar la consulta
                if ($stmt->execute()) {
                    // Mostrar mensaje de éxito si se modificó el aviso correctamente
                    echo "<div class='alert alert-success' role='alert'>El aviso se modificó con éxito.</div>";
                    echo "<a class='btn btn-primary' href='admin_avisos.php'>Regresar a la lista de avisos</a>";
                } else {
                    // Mostrar mensaje de error si ocurrió un problema al modificar el aviso
                    echo "<div class='alert alert-danger' role='alert'>Error al modificar el aviso: " . $stmt->error . "</div>";
                }

                // Cerrar la sentencia preparada
                $stmt->close();
            } else {
                // Obtener los datos actuales del aviso
                $consulta_aviso = "SELECT * FROM avisos WHERE id_aviso = $id_aviso";
                $resultado_aviso = $conexion->query($consulta_aviso);

                if ($resultado_aviso->num_rows == 1) {
                    $fila_aviso = $resultado_aviso->fetch_assoc();
                    $titulo_actual = $fila_aviso['titulo'];
                    $descripcion_actual = $fila_aviso['descripcion'];

                    // Mostrar el formulario para editar el aviso
                    echo "<form action='' method='post'>";
                    echo "<label for='titulo'>Título:</label>";
                    echo "<input class='form-control' type='text' name='titulo' value='$titulo_actual' required>";
                    echo "<br>";
                    echo "<label for='descripcion'>Descripción:</label>";
                    echo "<textarea class='form-control' name='descripcion' required>$descripcion_actual</textarea>";
                    echo "<br>";
                    echo "<input class='btn btn-primary' type='submit' name='submit' value='Guardar Cambios'>";
                    echo "<a class='btn btn-primary' href='admin_avisos.php'>Cancelar</a>";
                    echo "</form>";
                } else {
                    // Mostrar mensaje de error si no se encontró el aviso
                    echo "<div class='alert alert-danger' role='alert'>Error: No se encontró el aviso.</div>";
                }
            }
        } else {
            // Mostrar mensaje de error si el parámetro 'id' es inválido
            echo "<div class='alert alert-danger' role='alert'>Error: El parámetro 'id' es inválido.</div>";
        }

        // Cerrar la conexión a la base de datos
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
