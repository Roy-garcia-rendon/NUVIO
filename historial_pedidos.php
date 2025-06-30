<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
include("include/conexion.php");
$db = new conexion();
$conexion = $db->conex();

$usuario_id = $_SESSION['usuario_id'];
$resultado = $conexion->query("SELECT * FROM pedidos WHERE usuario_id = $usuario_id ORDER BY fecha DESC");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Historial de pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>📦 Historial de pedidos</h2>
        <?php if ($resultado->num_rows > 0): ?>
            <table class="table table-bordered mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Método de pago</th>
                        <th>Ver detalles</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($pedido = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?= $pedido['fecha'] ?></td>
                            <td>$<?= number_format($pedido['total'], 2) ?></td>
                            <td><?= $pedido['metodo_pago'] ?></td>
                            <td><a href="detalle_pedido.php?id=<?= $pedido['id'] ?>" class="btn btn-info btn-sm">Ver</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="mt-3">No tienes pedidos registrados.</p>
        <?php endif; ?>
    </div>
</body>

</html>