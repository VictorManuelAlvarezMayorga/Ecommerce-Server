
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>
</head>
<body>
    <p>Ya se veeeeeeeeeeeeee</p>
    <p>Ayrrrtooooon Seeenaaaaaaa Da Braziiiiiiiiiil</p>

    <?php
include "dbcon.php";

echo "¡Conexión exitosa a la base de datos ecommerce!";

// Ejemplo: mostrar productos
$resultado = $conexion->query("SELECT * FROM productos");

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        echo "<p>" . $fila['nombre'] . "</p>"; // ajusta 'nombre' según tu columna real
    }
} else {
    echo "No hay productos registrados.";
}
?>
</body>
</html>