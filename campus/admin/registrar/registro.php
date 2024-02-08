<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h2>Registrar Nuevo Usuario</h2><br>
        <form action="procesar_registro.php" method="post">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" required><br>
            
            <label for="correo">Correo Electrónico:</label>
            <input type="email" name="correo" required><br>
            
            <label for="contrasena">Contraseña:</label>
            <input type="password" name="contrasena" required><br>
            
            <label for="rol">Rol:</label>
            <select name="rol">
                <option value="alumno">Alumno</option>
                <option value="profesor">Profesor</option>
                <option value="administrador">Administrador</option>
            </select><br>
            
            <input type="submit" value="Registrar">
        </form>
</body>
</html>