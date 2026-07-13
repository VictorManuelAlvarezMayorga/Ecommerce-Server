<?php
include "dbcon.php";

$nombre = $_POST['nombre'];
$apellidopaterno = $_POST['apellidopaterno'];
$apellidomaterno = $_POST['apellidomaterno'];
$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$rol = "cliente";      // valor por defecto
$estatus = 1;           // 1 = activo
$medio = "web";         // valor por defecto

$sql = "INSERT INTO usuarios (nombre, apellidopaterno, apellidomaterno, username, password, rol, estatus, medio) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ssssssss", $nombre, $apellidopaterno, $apellidomaterno, $username, $password, $rol, $estatus, $medio);

if ($stmt->execute()) {
    header("Location: login.php");
} else {
    echo "Error al registrar: " . $conexion->error;
}
?>