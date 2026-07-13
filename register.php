<!DOCTYPE html>
<html>
<head>
    <title>Registro</title>
</head>
<body>
    <h2>Crear cuenta</h2>
    <form action="process_registration.php" method="POST">
        <label>Nombre:</label><br>
        <input type="text" name="nombre" required><br><br>

        <label>Apellido paterno:</label><br>
        <input type="text" name="apellidopaterno" required><br><br>

        <label>Apellido materno:</label><br>
        <input type="text" name="apellidomaterno"><br><br>

        <label>Crea tu nombre de Usuario:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Contraseña:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Registrarme</button>
    </form>
    <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
</body>
</html>