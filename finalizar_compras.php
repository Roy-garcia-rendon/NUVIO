<?php
session_start();
include("include/conexion.php");

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['carrito_id'])) {
    header("Location: login.php");
    exit();
}

$db = new conexion();
$conexion = $db->conex();

// Obtener productos del carrito desde la base de datos
$carrito_id = $_SESSION['carrito_id'];
$sql = "SELECT p.nombre, p.precio, cp.cantidad 
        FROM carrito_productos cp 
        JOIN productos p ON cp.producto_id = p.id 
        WHERE cp.carrito_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $carrito_id);
$stmt->execute();
$resultado = $stmt->get_result();

$total = 0;
$productos = [];

while ($row = $resultado->fetch_assoc()) {
    $productos[] = $row;
    $total += $row['precio'] * $row['cantidad'];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Resumen de compra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Resumen de tu pedido 🧾</h2>

        <?php if (empty($productos)): ?>
            <div class="alert alert-warning">Tu carrito está vacío.</div>
        <?php else: ?>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['nombre']) ?></td>
                            <td><?= $item['cantidad'] ?></td>
                            <td>$<?= number_format($item['precio'], 2) ?></td>
                            <td>$<?= number_format($item['precio'] * $item['cantidad'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="table-secondary">
                        <td colspan="3"><strong>Total</strong></td>
                        <td><strong>$<?= number_format($total, 2) ?></strong></td>
                    </tr>
                </tbody>
            </table>

            <form action="pago.php" method="post">
                <input type="hidden" name="total" value="<?= $total ?>">
                <button type="submit" class="btn btn-success">Ir al pago</button>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>