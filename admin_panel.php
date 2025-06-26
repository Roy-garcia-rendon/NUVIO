<?php
session_start();
include 'include/conexion.php';
$db = new conexion();
$conexion = $db->conex();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit();
}

// Obtener el tipo de usuario desde la base de datos
$usuario_id = $_SESSION['usuario_id'];
$stmt = $conexion->prepare("SELECT tipo FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($fila = $resultado->fetch_assoc()) {
    if ($fila['tipo'] !== 'admin') {
        // Si no es admin, redirigir al index
        header('Location: index.php');
        exit();
    }
} else {
    // Si no se encuentra el usuario, redirigir al index
    header('Location: index.php');
    exit();
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - NUVIO</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .admin-container {
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

        .admin-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f8f9fa;
        }

        .admin-title {
            color: #2c3e50;
            font-weight: 700;
            font-size: 2.5rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .admin-subtitle {
            color: #666;
            font-size: 1.1rem;
            margin-top: 0.5rem;
        }

        .action-buttons {
            margin-bottom: 2rem;
            text-align: center;
        }

        .btn {
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            margin: 0.5rem;
            padding: 0.75rem 1.5rem;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745, #20c997);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #495057);
        }

        .btn-warning {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }

        .table {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            background: white;
        }

        .table thead {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .table thead th {
            border: none;
            font-weight: 600;
            padding: 1.5rem 1rem;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.1);
            transform: scale(1.01);
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        .product-name {
            font-weight: 600;
            color: #2c3e50;
        }

        .product-description {
            color: #666;
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .product-price {
            font-weight: 700;
            color: #667eea;
            font-size: 1.1rem;
        }

        .product-stock {
            font-weight: 600;
            color: #28a745;
        }

        .action-buttons-cell {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }

        .action-buttons-cell .btn {
            margin: 0;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
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
            background: rgba(255, 255, 255, 0.1);
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

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 1.5rem;
            border-radius: 15px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            border-radius: 15px 15px 0 0 !important;
            border: none;
            font-weight: 600;
        }

        .btn-outline-secondary:hover,
        .btn-outline-success:hover,
        .btn-outline-warning:hover,
        .btn-outline-info:hover,
        .btn-outline-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        @media (max-width: 768px) {
            .admin-container {
                margin: 1rem;
                padding: 1rem;
            }

            .admin-title {
                font-size: 2rem;
            }

            .table-responsive {
                font-size: 0.9rem;
            }

            .action-buttons-cell {
                flex-direction: column;
                gap: 0.25rem;
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
        <div class="admin-container">
            <div class="admin-header">
                <h1 class="admin-title">
                    <i class="fas fa-cog me-3"></i>
                    Panel de Administración
                </h1>
                <p class="admin-subtitle">Gestiona los productos de tu tienda</p>
            </div>

            <?php
            // Obtener estadísticas
            $total_productos = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM productos"))['total'];
            $total_usuarios = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM usuarios"))['total'];
            $productos_stock_bajo = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM productos WHERE stock < 10"))['total'];
            ?>

            <!-- Estadísticas -->
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_productos; ?></div>
                    <div class="stat-label">Productos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_usuarios; ?></div>
                    <div class="stat-label">Usuarios</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $productos_stock_bajo; ?></div>
                    <div class="stat-label">Stock Bajo</div>
                </div>
            </div>

            <!-- Herramientas de Administración -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-tools me-2"></i>
                                Herramientas de Administración
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <h6 class="text-primary mb-2">
                                        <i class="fas fa-bug me-1"></i>Debug y Testing
                                    </h6>
                                    <div class="d-grid gap-2">
                                        <a href="admin_tools/debug_simple.php" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-search me-1"></i>Debug Simple
                                        </a>
                                        <a href="admin_tools/debug_index.php" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-list me-1"></i>Debug Index
                                        </a>
                                        <a href="admin_tools/index_debug.php" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-home me-1"></i>Index Debug
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <h6 class="text-success mb-2">
                                        <i class="fas fa-images me-1"></i>Gestión de Imágenes
                                    </h6>
                                    <div class="d-grid gap-2">
                                        <a href="admin_tools/test_imagen.php" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-eye me-1"></i>Test Imagen
                                        </a>
                                        <a href="admin_tools/test_mostrar_imagen.php" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-image me-1"></i>Test Mostrar Imagen
                                        </a>
                                        <a href="admin_tools/mostrar_imagen.php" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-photo-video me-1"></i>Mostrar Imagen
                                        </a>
                                        <a href="admin_tools/mover_imagen_defecto.php" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-exchange-alt me-1"></i>Mover Imagen Defecto
                                        </a>
                                        <a href="admin_tools/setup_imagenes.php" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-cog me-1"></i>Setup Imágenes
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <h6 class="text-warning mb-2">
                                        <i class="fas fa-check-circle me-1"></i>Verificación
                                    </h6>
                                    <div class="d-grid gap-2">
                                        <a href="admin_tools/verificar_productos.php" class="btn btn-outline-warning btn-sm">
                                            <i class="fas fa-box me-1"></i>Verificar Productos
                                        </a>
                                        <a href="admin_tools/verificar_tipo_imagen.php" class="btn btn-outline-warning btn-sm">
                                            <i class="fas fa-file-image me-1"></i>Verificar Tipo Imagen
                                        </a>
                                        <a href="admin_tools/verificar_y_agregar_imagenes.php" class="btn btn-outline-warning btn-sm">
                                            <i class="fas fa-plus-circle me-1"></i>Verificar y Agregar Imágenes
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <h6 class="text-info mb-2">
                                        <i class="fas fa-stethoscope me-1"></i>Diagnóstico
                                    </h6>
                                    <div class="d-grid gap-2">
                                        <a href="admin_tools/diagnostico_imagenes_detallado.php" class="btn btn-outline-info btn-sm">
                                            <i class="fas fa-clipboard-list me-1"></i>Diagnóstico Imágenes
                                        </a>
                                        <a href="admin_tools/DIAGNOSTICO_IMAGENES.md" class="btn btn-outline-info btn-sm">
                                            <i class="fas fa-file-alt me-1"></i>Documentación Diagnóstico
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <h6 class="text-danger mb-2">
                                        <i class="fas fa-flask me-1"></i>Pruebas
                                    </h6>
                                    <div class="d-grid gap-2">
                                        <a href="admin_tools/agregar_productos_prueba.php" class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-plus me-1"></i>Agregar Productos Prueba
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="action-buttons">
                <a class="btn btn-success" href="agregar_producto.php">
                    <i class="fas fa-plus me-2"></i>
                    Agregar producto
                </a>
                <a class="btn btn-secondary" href="index.php">
                    <i class="fas fa-home me-2"></i>
                    Volver al inicio
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><i class="fas fa-box me-2"></i>Nombre</th>
                            <th><i class="fas fa-align-left me-2"></i>Descripción</th>
                            <th><i class="fas fa-tag me-2"></i>Precio</th>
                            <th><i class="fas fa-boxes me-2"></i>Stock</th>
                            <th><i class="fas fa-cogs me-2"></i>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $resultado = mysqli_query($conexion, "SELECT * FROM productos ORDER BY nombre");
                        while ($producto = mysqli_fetch_assoc($resultado)) {
                            $stock_class = $producto['stock'] < 10 ? 'text-danger' : 'text-success';
                        ?>
                            <tr>
                                <td class="product-name"><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                <td class="product-description" title="<?php echo htmlspecialchars($producto['descripcion']); ?>">
                                    <?php echo htmlspecialchars($producto['descripcion']); ?>
                                </td>
                                <td class="product-price">$<?php echo number_format($producto['precio'], 2); ?></td>
                                <td class="product-stock <?php echo $stock_class; ?>">
                                    <i class="fas fa-box me-1"></i>
                                    <?php echo $producto['stock']; ?>
                                </td>
                                <td class="action-buttons-cell">
                                    <a class="btn btn-warning btn-sm" href="editar_producto.php?id=<?php echo $producto['id']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a class="btn btn-danger btn-sm" href="admin_panel.php?eliminar=<?php echo $producto['id']; ?>"
                                        onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
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

            // Animación para las tarjetas de estadísticas
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 200 * index);
            });
        });
    </script>
</body>

</html>