<?php

//Por si estamos en el servidor local (XAMPP) o en el servidor real
$esLocal = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1']);

if ($esLocal) {
$host = "127.0.0.1";
$usuario = "root";
$password = "";
$basedatos = "ecommerce";
} else {
    // Configuración del Servidor de Filezila
    $host = "datallizer.com";
    $usuario = "datallizer_utmauser";
    $password = "proyectoUtma2026";
    $basedatos = "datallizer_ecommerce";
}

//conexion
$con = new mysqli($host, $usuario, $password, $basedatos);

//Conexion Falló?
if ($con->connect_error) {
    die("Error de conexión: " . $con->connect_error);
}

//asegurar que los caracteres especiales se lean correctamente (acentos, ñ, etc.)
mysqli_set_charset($con, "utf8mb4");
?>