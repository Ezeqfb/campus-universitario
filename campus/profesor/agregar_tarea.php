<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Tarea</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <div class="container">
        <h2>Agregar Nueva Tarea</h2>
        <?php
        // Incluir el archivo de conexión a la base de datos
        include('../php/conexion.php');

        // Obtener el ID del curso de la URL
        $id_curso = isset($_GET['id_curso']) ? $_GET['id_curso'] : '';
        echo "ID del curso: " . $id_curso;

        // Verificar si el ID del curso está presente
        if (!empty($id_curso)) {
            // Verificar si se ha enviado el formulario
            if (isset($_POST['submit'])) {
                // Obtener los datos del formulario
                $titulo = isset($_POST['titulo']) ? $_POST['titulo'] : '';
                $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
                $fecha_creacion = date('Y-m-d');

                // Insertar la tarea en la base de datos
                $sql_insert = "INSERT INTO tareas_curso (id_curso, titulo, descripcion, fecha_creacion) VALUES (?, ?, ?, ?)";
                $stmt_insert = $conexion->prepare($sql_insert);
                $stmt_insert->bind_param('isss', $id_curso, $titulo, $descripcion, $fecha_creacion);
                if ($stmt_insert->execute()) {
                    echo "<div class='alert alert-success' role='alert'>Tarea agregada exitosamente</div>";
                } else {
                    echo "<div class='alert alert-danger' role='alert'>Error al agregar la tarea: " . $stmt_insert->error . "</div>";
                }
            }
        } else {
            // Si el ID del curso no está presente en la URL
            echo "<div class='alert alert-danger' role='alert'>ID del curso no proporcionado</div>";
        }

        // Cerrar la conexión
        $conexion->close();
        ?>

        <!-- Formulario para agregar una nueva tarea -->
        <form action="agregar_tarea.php?id_curso=<?php echo $id_curso; ?>" method="POST">
            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" class="form-control" id="titulo" name="titulo" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
            </div>
            <input type="hidden" name="id_curso" value="<?php echo $id_curso; ?>">
            <br>
            <button type="submit" name="submit" class="btn btn-primary">Agregar Tarea</button>
        </form>
    </div>
    <script src="../js/jquery.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
</body>
</html>
