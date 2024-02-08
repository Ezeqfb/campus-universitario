<!DOCTYPE html>
<html>
<head>
    <title>Editar Curso</title>
</head>
<body>
    <?php
    // Obtener el id del curso de la url
    $id_curso = isset($_GET['id']) ? $_GET['id'] : '';

    if ($id_curso !== '') {
        echo "ID del curso: " . $id_curso;
        // Muestra el formulario solo si se proporcionó un ID válido
    ?>

        <h2>Editar Curso</h2>
        <form action="procesar_edicion_curso.php" method="post">
            <input type="hidden" name="id_curso" value="<?php echo $id_curso; ?>">
            <label for="nombre">Nombre del Curso:</label>
            <input type="text" name="nombre" required><br>
            <input type="submit" value="Guardar Cambios">
        </form>
        
    <?php
    } else {
        // Si no se proporciona un id válido, muestra un mensaje de error.
        echo "El curso seleccionado no existe.";
    }
    ?>
</body>
</html>