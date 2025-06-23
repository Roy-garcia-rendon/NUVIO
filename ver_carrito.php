<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Carrito de Compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-body">
                <h1 class="card-title text-center mb-4">🛍️ Tu carrito de compras</h1>

                <?php if (empty($_SESSION['carrito'])): ?>
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
                                $total = 0;
                                foreach ($_SESSION['carrito'] as $id => $producto) {
                                    $subtotal = $producto['precio'] * $producto['cantidad'];
                                    $total += $subtotal;
                                ?>
                                    <tr>
                                        <td><?= $producto['nombre'] ?></td>
                                        <td>$<?= number_format($producto['precio'], 2) ?></td>
                                        <td>
                                            <form action="actualizar_carrito.php" method="post" class="d-flex justify-content-center">
                                                <input type="hidden" name="id" value="<?= $id ?>">
                                                <input type="number" name="cantidad" value="<?= $producto['cantidad'] ?>" min="1" class="form-control form-control-sm me-2" style="width:80px;">
                                                <button type="submit" class="btn btn-sm btn-outline-primary">Actualizar</button>
                                            </form>
                                        </td>
                                        <td>$<?= number_format($subtotal, 2) ?></td>
                                        <td>
                                            <form method="post" action="eliminar_del_carrito.php">
                                                <input type="hidden" name="id" value="<?= $id ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <tr class="table-secondary">
                                    <td colspan="3" class="text-end"><strong>Total general:</strong></td>
                                    <td colspan="2"><strong>$<?= number_format($total, 2) ?></strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <div class="mt-4 text-center">
                    <a href="index.php" class="btn btn-primary">← Seguir comprando</a>
                </div>

                <?php if (!empty($_SESSION['carrito'])): ?>
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