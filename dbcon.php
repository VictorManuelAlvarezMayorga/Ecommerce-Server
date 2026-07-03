<?php
$host = "127.0.0.1";
$usuario = "root";
$password = "";
$basedatos = "ecommerce";

$conexion = new mysqli($host, $usuario, $password, $basedatos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>