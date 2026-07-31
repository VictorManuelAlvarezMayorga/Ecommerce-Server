<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'dbcon.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: tienda-en-linea.php');
    exit;
}

$identificador = $_GET['id'];

$stmt = $con->prepare("
    SELECT *
    FROM pedidos
    WHERE identificador = ?
    LIMIT 1
");

if (!$stmt) {
    die($con->error);
}

$stmt->bind_param('s', $identificador);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: tienda-en-linea.php');
    exit;
}

$pedido = $result->fetch_assoc();

// Si el pedido todavía no está pagado ni tiene una referencia SPEI pendiente, no debería ver esta pantalla
if (
    strtolower($pedido['status_pago']) !== 'pagado' &&
    strtolower($pedido['status_pago']) !== 'pendiente spei'
) {
    header('Location: pago.php?id=' . urlencode($identificador));
    exit;
}

$ventas = [];

$stmtVentas = $con->prepare("
    SELECT titulo, sku, cantidad, precio, descuento
    FROM ventas
    WHERE identificador = ?
");

if (!$stmtVentas) {
    die($con->error);
}

$stmtVentas->bind_param('s', $identificador);
$stmtVentas->execute();
$resultVentas = $stmtVentas->get_result();

while ($row = $resultVentas->fetch_assoc()) {
    $ventas[] = $row;
}

$esPagado = strtolower($pedido['status_pago']) === 'pagado';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/menu.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/ico.ico" />
    <title>Pedido confirmado | Mi Empresa</title>
</head>
<style>
    body {
        background-color: #ecf0f3;
    }
</style>

<body>
    <?php include('menu.php'); ?>

    <div class="container-fluid bg-light">
        <div class="row mt-5 justify-content-center">
            <div class="col-11 col-md-7 mt-5 mb-5 p-5" style="background-color: #ffffff; border-radius: 15px;">

                <div class="text-center mb-4">
                    <?php if ($esPagado): ?>
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                        <h2 class="mt-3">¡Gracias por tu compra!</h2>
                        <p class="text-muted">Tu pago se procesó correctamente.</p>
                    <?php else: ?>
                        <i class="bi bi-hourglass-split text-warning" style="font-size: 4rem;"></i>
                        <h2 class="mt-3">Pedido registrado</h2>
                        <p class="text-muted">Estamos esperando la confirmación de tu pago por SPEI.</p>
                    <?php endif; ?>
                </div>

                <div class="p-3 mb-4" style="background-color: #e7e7e7; border-radius: 10px;">
                    <p class="mb-1"><b>Número de pedido:</b> <?= htmlspecialchars($pedido['identificador'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p class="mb-1"><b>Nombre:</b> <?= htmlspecialchars($pedido['nombre'], ENT_QUOTES, 'UTF-8'); ?> <?= htmlspecialchars($pedido['apellidop'], ENT_QUOTES, 'UTF-8'); ?> <?= htmlspecialchars($pedido['apellidom'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p class="mb-1"><b>Correo:</b> <?= htmlspecialchars($pedido['email'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p class="mb-0"><b>Envío a:</b> <?= htmlspecialchars($pedido['calle'], ENT_QUOTES, 'UTF-8'); ?> #<?= htmlspecialchars($pedido['exterior'], ENT_QUOTES, 'UTF-8'); ?>, <?= htmlspecialchars($pedido['colonia'], ENT_QUOTES, 'UTF-8'); ?>, <?= htmlspecialchars($pedido['ciudad'], ENT_QUOTES, 'UTF-8'); ?>, <?= htmlspecialchars($pedido['estado'], ENT_QUOTES, 'UTF-8'); ?>. CP <?= htmlspecialchars($pedido['postal'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>

                <h5>Tus productos</h5>
                <?php foreach ($ventas as $item): ?>
                    <div class="row py-2 border-bottom">
                        <div class="col-8">
                            <p class="mb-0"><strong><?= (int)$item['cantidad'] ?> x <?= htmlspecialchars($item['titulo']) ?></strong></p>
                            <p class="mb-0 small text-muted">SKU: <?= htmlspecialchars($item['sku']) ?></p>
                        </div>
                        <div class="col-4 text-end">
                            <?php
                            $monto = $item['cantidad'] * $item['precio'];
                            $disc = $item['cantidad'] * $item['descuento'];
                            ?>
                            <p class="mb-0">$<?= number_format($monto, 2) ?></p>
                            <?php if ($disc > 0): ?>
                                <p class="mb-0 small text-success">-$<?= number_format($disc, 2) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="text-end mt-3">
                    <p class="mb-1"><b>Subtotal:</b> $<?= number_format($pedido['subtotal'], 2); ?></p>
                    <?php if ($pedido['cuponMonto'] > 0): ?>
                        <p class="mb-1 text-success"><b>Cupón:</b> -$<?= number_format($pedido['cuponMonto'], 2); ?></p>
                    <?php endif; ?>
                    <p class="mb-1"><b>Envío:</b> <?= $pedido['envioMonto'] > 0 ? '$' . number_format($pedido['envioMonto'], 2) : 'GRATIS'; ?></p>
                    <p style="font-weight: 600;"><b>Total:</b> $<?= number_format($pedido['total'], 2); ?></p>
                </div>

                <?php if (!$esPagado && !empty($pedido['clabe'])): ?>
                    <div class="p-3 mt-4" style="background-color: #2c3b5c; color: #fff; border-radius: 10px;">
                        <p class="mb-1"><strong>Banco:</strong> <?= htmlspecialchars($pedido['banco'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="mb-1"><strong>CLABE:</strong> <?= htmlspecialchars($pedido['clabe'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="mb-1"><strong>Referencia:</strong> <?= htmlspecialchars($pedido['referencia'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="mb-1"><strong>Convenio CIE:</strong> <?= htmlspecialchars($pedido['convenio'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php if (!empty($pedido['vigencia'])): ?>
                            <p class="mb-0"><strong>Vigencia:</strong> <?= htmlspecialchars($pedido['vigencia'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="text-center mt-4">
                    <a href="tienda-en-linea.php" class="btn btn-danger">Seguir comprando</a>
                </div>

            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
</body>

</html>