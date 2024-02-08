<?php
// Obtener datos del formulario
$id_curso = $_POST['id_curso'];
$alumnos_seleccionados = $_POST['alumnos'];

// Incluir el archivo de conexión a la base de datos
include('../../php/conexion.php');

// Insertar registros en la tabla inscripciones_curso
foreach ($alumnos_seleccionados as $id_alumno) {
    $sql = "INSERT INTO inscripciones_curso (id_curso, id_alumno) VALUES ($id_curso, $id_alumno)";
    $result = $conexion->query($sql);

    if (!$result) {
        echo "Error al agregar alumnos: " . $conexion->error;
        exit();
    }
}

$conexion->close();

// Redirigir o mostrar mensaje de éxito
header("Location: admin_cursos.php?mensaje=Alumnos agregados correctamente&id=$id_curso");
exit();
?>