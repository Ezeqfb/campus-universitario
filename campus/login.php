<?php
session_start();

// Verificar si ya hay una sesión activa
if (isset($_SESSION['id_usuario'])) {
    // Si ya hay una sesión activa, obtener el rol del usuario
    $rol = $_SESSION['rol'];
    
    // Redirigir al usuario según su rol
    switch ($rol) {
        case 'alumno':
            header("Location: alumno/pagina_alumno.php");
            exit();
        case 'profesor':
            header("Location: profesor/pagina_profesor.php");
            exit();
        case 'administrador':
            header("Location: admin/pagina_administrador.php");
            exit();
        default:
            // Si el rol no coincide con ninguno de los anteriores, mostrar un mensaje de error
            echo "Acceso no autorizado.";
            exit();
    }
}

$mensaje = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include('php/conexion.php'); // Incluye el archivo de conexión

    $email = mysqli_real_escape_string($conexion, $_POST['email']);

    // Consulta para obtener el rol del usuario
    $consulta = "SELECT id_usuario, contrasena, rol FROM usuarios WHERE correo = ?";
    $stmt = $conexion->prepare($consulta);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows == 1) {
        $fila = $resultado->fetch_assoc();

        $contrasena = $_POST['contrasena'];

        // Verifica la contraseña
        if (password_verify($contrasena, $fila['contrasena'])) {
            $_SESSION['id_usuario'] = $fila['id_usuario'];
            $_SESSION['rol'] = $fila['rol'];

            // Redirige al usuario según su rol
            switch ($fila['rol']) {
                case 'alumno':
                    header("Location: alumno/pagina_alumno.php");
                    exit();
                case 'profesor':
                    header("Location: profesor/pagina_profesor.php");
                    exit();
                case 'administrador':
                    header("Location: admin/pagina_administrador.php");
                    exit();
                default:
                    echo "Acceso no autorizado.";
                    break;
            }
        } else {
            $mensaje = "<div class='alert alert-warning' role='alert'>Usuario o contraseña incorrectos.</div>";
        }
    } else {    
        $mensaje = "<div class='alert alert-warning' role='alert'>Usuario o contraseña incorrectos.</div>";
    }

    $conexion->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/estilos.css">
    <title>Iniciar Sesión</title>
</head>
<body id="index-body">

    <!-- Header -->
    <header class="bg-white">
        <nav class="navbar navbar-expand navbar-light container">
            <div class="container-fluid">
                <!-- Nombre del sitio o logo -->
                <img id="logo" src="media/img/cudi1.png" alt="">

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
                        <!-- Botón para cerrar sesión -->
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">ACCEDER</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Inicio Section -->
    <section id="home">
        <div class="container">
            <div class="txt">
                <div id="login">
                    <h2>Iniciar Sesión</h2>
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="email">Correo Electrónico:</label>
                            <input class="form-control" type="email" name="email" required><br>
                        </div>
                        <div class="form-group">
                            <label for="contrasena">Contraseña:</label>
                            <input class="form-control" type="password" name="contrasena" required autocomplete="off"><br>
                        </div>
                        <?php
                            echo $mensaje;
                        ?>
                        <input class="btn btn-primary" type="submit" value="Iniciar Sesión">
                        <!--<p>¿Has olvidado tu contraseña? <a href="#">Recuperar contraseña.</a></p>-->
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Inicio Section -->
    <section>
    <div class="container">
        <div class="seccion">
            <div class="row">
                <div class="col-md-12">
                    <h3>Dirección de Educación a Distancia y Tecnología Educativa</h3>
                    <hr class="container">
                    <h5>Secretaría de Asuntos Académicos</h5>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="caja">
                        <img class="icons" src="media/img/clave.png" alt="">
                        <a class="links" href=""><h4>Olvidé mi clave</h4></a>
                        <p>Recuperar contraseña</p>
                    </div>
                    <div class="caja">
                        <img class="icons" src="media/img/sysacad.png" alt="">
                        <a class="links" href=""><h4>SYSACAD UTN</h4></a>
                        <p>Notas</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="caja">
                        <img class="icons" src="media/img/cudi.png" alt="">
                        <a class="links" href="https://www.cudi.ar/"><h4>Web CUDI</h4></a>
                        <p>Sitio web institucional</p>
                    </div>
                    <div class="caja">
                        <img class="icons" src="media/img/bedelia.png" alt="">
                        <a class="links" href=""><h4>Contacto bedelía</h4></a>
                        <p>Email de contacto: tusicudi@gmail.com</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <h4>Proyecto desarrollado por Fernando Bernal</h4>
    </footer>
    
</body>
</html>
