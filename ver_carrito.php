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
    <title>Carrito de Compras - NUVIO</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .cart-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin: 2rem auto;
            max-width: 1200px;
            transform: translateY(20px);
            opacity: 0;
            animation: slideUp 0.8s ease forwards;
        }

        @keyframes slideUp {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .cart-title {
            text-align: center;
            color: #2c3e50;
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 2rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .table {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            background: white;
        }

        .table-dark {
            background: linear-gradient(135deg, #667eea, #764ba2) !important;
            border: none;
        }

        .table-dark th {
            border: none;
            font-weight: 600;
            padding: 1.5rem 1rem;
        }

        .table-striped>tbody>tr:nth-of-type(odd) {
            background-color: rgba(102, 126, 234, 0.05);
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.1);
            transform: scale(1.01);
        }

        .form-control {
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            transform: translateY(-2px);
        }

        .btn {
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .btn-outline-primary {
            border: 2px solid #667eea;
            color: #667eea;
        }

        .btn-outline-primary:hover {
            background: #667eea;
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }

        .btn-outline-danger {
            border: 2px solid #dc3545;
            color: #dc3545;
        }

        .btn-outline-danger:hover {
            background: #dc3545;
            color: white;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .alert {
            border-radius: 15px;
            border: none;
            margin-bottom: 2rem;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .total-row {
            background: linear-gradient(135deg, #667eea, #764ba2) !important;
            color: white;
            font-weight: 700;
        }

        .total-row td {
            border: none;
            padding: 1.5rem 1rem;
        }

        .action-buttons {
            text-align: center;
            margin-top: 2rem;
        }

        .action-buttons .btn {
            margin: 0.5rem;
            padding: 0.75rem 2rem;
        }

        .product-name {
            font-weight: 600;
            color: #2c3e50;
        }

        .price-text {
            font-weight: 700;
            color: #667eea;
            font-size: 1.1rem;
        }

        .quantity-input {
            max-width: 100px;
            text-align: center;
        }

        .floating-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
            pointer-events: none;
        }

        .shape {
            position: absolute;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            width: 60px;
            height: 60px;
            top: 10%;
            left: 5%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 80px;
            height: 80px;
            top: 70%;
            right: 5%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 40px;
            height: 40px;
            bottom: 10%;
            left: 15%;
            animation-delay: 4s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-15px) rotate(180deg);
            }
        }

        .empty-cart {
            text-align: center;
            padding: 3rem;
            color: #666;
        }

        .empty-cart i {
            font-size: 4rem;
            color: #667eea;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .cart-container {
                margin: 1rem;
                padding: 1rem;
            }

            .cart-title {
                font-size: 2rem;
            }

            .table-responsive {
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>
    <!-- Floating background shapes -->
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="container-fluid">
        <div class="cart-container">
            <h1 class="cart-title">
                <i class="fas fa-shopping-cart me-3"></i>
                Tu carrito de compras
            </h1>

            <?php
            // Mostrar mensajes de éxito o error
            if (isset($_GET['success'])) {
                if ($_GET['success'] == '1') {
                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>¡Éxito!</strong> La cantidad se actualizó correctamente.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                          </div>';
                } elseif ($_GET['success'] == 'removed') {
                    echo '<div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Producto eliminado</strong> El producto se eliminó del carrito.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                          </div>';
                }
            }

            if (isset($_GET['error'])) {
                $error_messages = [
                    'update' => 'Error al actualizar la cantidad. Inténtalo de nuevo.',
                    'delete' => 'Error al eliminar el producto. Inténtalo de nuevo.',
                    'not_found' => 'El producto no se encontró en el carrito.',
                    'missing_data' => 'Datos faltantes para la actualización.'
                ];

                $error_msg = isset($error_messages[$_GET['error']]) ? $error_messages[$_GET['error']] : 'Ocurrió un error inesperado.';

                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Error:</strong> ' . $error_msg . '
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                      </div>';
            }

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
                <div class="empty-cart">
                    <i class="fas fa-shopping-bag"></i>
                    <h3>Tu carrito está vacío</h3>
                    <p class="text-muted">Agrega algunos productos para comenzar a comprar</p>
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Ir a la tienda
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped align-middle text-center">
                        <thead class="table-dark">
                            <tr>
                                <th><i class="fas fa-box me-2"></i>Producto</th>
                                <th><i class="fas fa-tag me-2"></i>Precio</th>
                                <th><i class="fas fa-sort-numeric-up me-2"></i>Cantidad</th>
                                <th><i class="fas fa-calculator me-2"></i>Total</th>
                                <th><i class="fas fa-cogs me-2"></i>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($producto = mysqli_fetch_assoc($resultado)) {
                                $subtotal = $producto['precio'] * $producto['cantidad'];
                                $total += $subtotal;
                            ?>
                                <tr>
                                    <td class="product-name"><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                    <td class="price-text">$<?php echo number_format($producto['precio'], 2); ?></td>
                                    <td>
                                        <form action="actualizar_carrito.php" method="post" class="d-flex justify-content-center" onsubmit="return validarCantidad(this);">
                                            <input type="hidden" name="id" value="<?php echo $producto['producto_id']; ?>">
                                            <input type="number" name="cantidad" value="<?php echo $producto['cantidad']; ?>" min="1" max="99" class="form-control form-control-sm me-2 quantity-input" required>
                                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="price-text">$<?php echo number_format($subtotal, 2); ?></td>
                                    <td>
                                        <form method="post" action="eliminar_del_carrito.php" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este producto?');">
                                            <input type="hidden" name="id" value="<?php echo $producto['producto_id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php }
                            $stmt->close();
                            ?>
                            <tr class="total-row">
                                <td colspan="3" class="text-end"><strong>Total general:</strong></td>
                                <td colspan="2"><strong>$<?php echo number_format($total, 2); ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="action-buttons">
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Seguir comprando
                    </a>

                    <?php if ($total > 0): ?>
                        <form action="vaciar_carrito.php" method="post" onsubmit="return confirm('¿Estás seguro de que deseas vaciar el carrito?');" style="display: inline;">
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="fas fa-trash me-2"></i>
                                Vaciar carrito
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function validarCantidad(form) {
            const cantidad = form.querySelector('input[name="cantidad"]').value;
            if (cantidad < 1) {
                alert('La cantidad debe ser mayor a 0');
                return false;
            }
            if (cantidad > 99) {
                alert('La cantidad máxima es 99');
                return false;
            }
            return true;
        }

        // Animación de entrada para las filas de la tabla
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateX(-20px)';
                setTimeout(() => {
                    row.style.transition = 'all 0.6s ease';
                    row.style.opacity = '1';
                    row.style.transform = 'translateX(0)';
                }, 100 * index);
            });
        });
    </script>
</body>

</html>