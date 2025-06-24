<?php
session_start();

// Inicializar conexión a la base de datos (siempre necesaria)
include_once 'include/conexion.php';
$db = new conexion();
$conexion = $db->conex();

// Verificar si el usuario es admin (consultando la BD)
$es_admin = false;
if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $stmt = $conexion->prepare("SELECT tipo, nombre FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($fila = $resultado->fetch_assoc()) {
        if ($fila['tipo'] === 'admin') {
            $es_admin = true;
        }
        // Guardar el nombre del usuario en sesión si no está ya guardado
        if (!isset($_SESSION['usuario_nombre'])) {
            $_SESSION['usuario_nombre'] = $fila['nombre'];
        }
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NUVIO - Tienda Online</title>
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

        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            border-radius: 0 0 20px 20px;
            margin-bottom: 2rem;
            animation: slideDown 0.8s ease;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: #667eea !important;
        }

        .nav-link {
            font-weight: 500;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 0 0.2rem;
        }

        .nav-link:hover {
            background: rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        .btn {
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .btn-success {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .btn-outline-danger {
            border: 2px solid #dc3545;
            color: #dc3545;
        }

        .btn-outline-danger:hover {
            background: #dc3545;
            color: white;
        }

        .btn-dark {
            background: linear-gradient(135deg, #2c3e50, #34495e);
        }

        .page-title {
            text-align: center;
            color: #2c3e50;
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 2rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: fadeInUp 1s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            overflow: hidden;
            animation: fadeInUp 0.8s ease;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
            transition: all 0.3s ease;
        }

        .card:hover .card-img-top {
            transform: scale(1.05);
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        .card-text {
            color: #666;
            line-height: 1.6;
        }

        .price {
            font-size: 1.2rem;
            font-weight: 700;
            color: #667eea;
        }

        .stock {
            font-size: 0.9rem;
            color: #28a745;
            font-weight: 500;
        }

        .card-footer {
            background: transparent;
            border-top: 1px solid #f8f9fa;
            padding: 1rem 1.5rem;
        }

        .welcome-text {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
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

        @media (max-width: 768px) {
            .page-title {
                font-size: 2rem;
            }

            .navbar {
                margin-bottom: 1rem;
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
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <i class="fas fa-shopping-bag me-2"></i>
                    NUVIO
                </a>

                <div class="navbar-nav ms-auto d-flex align-items-center">
                    <a class="btn btn-success me-3" href="ver_carrito.php">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Ver carrito
                    </a>

                    <?php if (isset($_SESSION["usuario_id"])): ?>
                        <span class="welcome-text me-3">
                            <i class="fas fa-user me-2"></i>
                            Bienvenido, <?php echo htmlspecialchars($_SESSION["usuario_nombre"]); ?>
                        </span>
                        <a class="btn btn-outline-danger" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>
                            Cerrar sesión
                        </a>
                    <?php else: ?>
                        <a class="nav-link me-3" href="login.php">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Inicia sesión
                        </a>
                        <a class="nav-link" href="registro.php">
                            <i class="fas fa-user-plus me-2"></i>
                            Regístrate
                        </a>
                    <?php endif; ?>

                    <?php if ($es_admin): ?>
                        <a class="btn btn-dark ms-3" href="admin_panel.php">
                            <i class="fas fa-cog me-2"></i>
                            Panel de administración
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="container">
            <h1 class="page-title">🛍️ Nuestros Productos</h1>

            <?php
            // Consulta de productos (reutilizamos la conexión ya establecida)
            $sql = "SELECT * FROM productos";
            $resultado = mysqli_query($conexion, $sql);
            ?>

            <!-- Product Grid -->
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                <?php
                $delay = 0;
                while ($row = mysqli_fetch_assoc($resultado)) :
                    $delay += 0.1;
                ?>
                    <div class="col" style="animation-delay: <?php echo $delay; ?>s;">
                        <div class="card h-100">
                            <img src="media/camisablanca.jpg" class="card-img-top" alt="<?php echo htmlspecialchars($row["nombre"]); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($row["nombre"]); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($row["descripcion"]); ?></p>
                                <p class="card-text">
                                    <span class="price">$<?php echo number_format($row["precio"], 2); ?></span>
                                </p>
                                <p class="card-text">
                                    <span class="stock">
                                        <i class="fas fa-box me-1"></i>
                                        Stock: <?php echo $row["stock"]; ?>
                                    </span>
                                </p>
                            </div>
                            <div class="card-footer text-center">
                                <a href="carrito.php?agregar=<?php echo $row['id']; ?>" class="btn btn-primary w-100">
                                    <i class="fas fa-cart-plus me-2"></i>
                                    Agregar al carrito
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <?php mysqli_close($conexion); ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Animación de entrada para las tarjetas
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card');

            // Intersection Observer para animaciones al hacer scroll
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, {
                threshold: 0.1
            });

            cards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'all 0.6s ease';
                observer.observe(card);
            });

            // Efecto hover mejorado para las tarjetas
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-10px) scale(1.02)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        });
    </script>
</body>

</html>