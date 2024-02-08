<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap/bootstrap.min.css">
    <title>Document</title>
</head>
<body>
    <h2>Eliminar Curso</h2>
        <p>¿Está seguro de que desea eliminar este curso?</p>
        <form action="procesar_eliminacion_curso.php" method="post">
            <input type="submit" value="Sí, Eliminar">
            <a href="admin_cursos.php">Cancelar</a>
        </form>
        
</body>
</html>