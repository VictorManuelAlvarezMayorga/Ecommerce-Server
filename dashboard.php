<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Panel principal</title>
</head>
<body>
    <h2>Bienvenido, <?php echo $_SESSION['usuario_nombre']; ?> 👋</h2>
    <p>Tu rol: <?php echo $_SESSION['usuario_rol']; ?></p>
    <a href="logout.php">Cerrar sesión</a>
</body>
</html>