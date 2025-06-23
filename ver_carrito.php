<?php
session_start();
require 'include/conexion.php'; // asegúrate de tener tu conexión a BD

// Crear conexión
$db = new conexion();
$conexion = $db->conex();

// Validar sesión y carrito_id
if (!isset($_SESSION['carrito_id'])) {
    echo "<div class='alert alert-danger text-center mt-5'>Carrito no disponible. Inicia sesión o agrega un producto para comenzar.</div>";
    exit;
}

$carrito_id = $_SESSION['carrito_id'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Carrito de Compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-body">
                <h1 class="card-title text-center mb-4">🛍️ Tu carrito de compras</h1>

                <?php
                // Inicializar total
                $total = 0;

                // Obtener los productos del carrito desde la BD
                $stmt = $conexion->prepare("
                    SELECT cp.producto_id, cp.cantidad, p.nombre, p.precio
                    FROM carrito_productos cp
                    JOIN productos p ON cp.producto_id = p.id
                    WHERE cp.carrito_id = ?
                ");
                $stmt->bind_param("i", $carrito_id);
                $stmt->execute();
                $resultado = $stmt->get_result();

                if ($resultado->num_rows === 0): ?>
                    <div class="alert alert-info text-center">
                        Tu carrito está vacío.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>Producto</th>
                                    <th>Precio</th>
                                    <th>Cantidad</th>
                                    <th>Total</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($producto = mysqli_fetch_assoc($resultado)) {
                                    $subtotal = $producto['precio'] * $producto['cantidad'];
                                    $total += $subtotal;
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                        <td>$<?php echo number_format($producto['precio'], 2); ?></td>
                                        <td>
                                            <form action="actualizar_carrito.php" method="post" class="d-flex justify-content-center">
                                                <input type="hidden" name="id" value="<?php echo $producto['producto_id']; ?>">
                                                <input type="number" name="cantidad" value="<?php echo $producto['cantidad']; ?>" min="1" class="form-control form-control-sm me-2" style="width:80px;">
                                                <button type="submit" class="btn btn-sm btn-outline-primary">Actualizar</button>
                                            </form>
                                        </td>
                                        <td>$<?php echo number_format($subtotal, 2); ?></td>
                                        <td>
                                            <form method="post" action="eliminar_del_carrito.php">
                                                <input type="hidden" name="id" value="<?php echo $producto['producto_id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php }
                                $stmt->close();
                                ?>
                                <tr class="table-secondary">
                                    <td colspan="3" class="text-end"><strong>Total general:</strong></td>
                                    <td colspan="2"><strong>$<?php echo number_format($total, 2); ?></strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <div class="mt-4 text-center">
                    <a href="index.php" class="btn btn-primary">← Seguir comprando</a>
                </div>

                <?php if ($total > 0): ?>
                    <div class="mt-2 text-center">
                        <form action="vaciar_carrito.php" method="post" onsubmit="return confirm('¿Estás seguro de que deseas vaciar el carrito?');">
                            <button type="submit" class="btn btn-outline-danger">🗑️ Vaciar carrito</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>