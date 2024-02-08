<?php
session_start();

// Verificar si se envió el formulario de cerrar sesión
if(isset($_POST['cerrar_sesion'])) {
    // Cerrar la sesión actual
    session_unset();
    session_destroy();

    // Redirigir a la página de inicio de sesión
    header("Location: ../login.php");
    exit();
} else {
    // Si no se envió el formulario, redirigir a la página de inicio de sesión
    header("Location: ../login.php");
    exit();
}
?>
