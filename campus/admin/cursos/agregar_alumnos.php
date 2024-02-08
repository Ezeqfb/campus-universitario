<?php
session_start();

// Verificar si hay una sesión activa y si el usuario es un administrador
if(isset($_SESSION['id_usuario']) && $_SESSION['rol'] === 'administrador') {
    // Obtener el ID del curso desde la URL
    $id_curso = $_GET['id'];

    // Incluir el archivo de conexión a la base de datos
    include('../../php/conexion.php');

    // Consulta para obtener la lista de alumnos
    $sqlAlumnos = "SELECT id_usuario, nombre FROM usuarios WHERE rol = 'alumno'";
    $resultadoAlumnos = $conexion->query($sqlAlumnos);

    if ($resultadoAlumnos) {
        $alumnos = $resultadoAlumnos->fetch_all(MYSQLI_ASSOC);
    } else {
        echo "Error en la consulta: " . mysqli_error($conexion);
    }

    $conexion->close();
} else {
    // Si no hay una sesión activa o el usuario no es administrador, redirigir a la página de inicio de sesión
    header("Location: ../../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/estilos.css">
    <title>Administrar Alumnos</title>
</head>
<body>
    <h1>Hola admin!</h1>

    <h2>Agregar Alumnos al Curso</h2>

    <!-- Modificar el formulario para enviar el id_curso y los ids de los alumnos -->
    <form action="procesar_agregar_alumnos.php" method="post">
        <input type="hidden" name="id_curso" value="<?php echo $id_curso; ?>">
        <?php foreach ($alumnos as $alumno) { ?>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="alumnos[]" value="<?php echo $alumno['id_usuario']; ?>">
                <label class="form-check-label" for="defaultCheck1">
                    <?php echo $alumno['nombre']; ?>
                </label>
            </div>
        <?php } ?>
        <br>
        <input type="submit" value="Agregar Alumnos">
    </form>
    
</body>
</html>