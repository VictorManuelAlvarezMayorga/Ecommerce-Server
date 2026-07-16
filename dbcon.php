<?php
$host = "127.0.0.1";
$usuario = "root";
$password = "";
$basedatos = "ecommerce";

//conexion
$con = new mysqli($host, $usuario, $password, $basedatos);

if ($con->connect_error) {
    die("Error de conexión: " . $con->connect_error);
}
?>