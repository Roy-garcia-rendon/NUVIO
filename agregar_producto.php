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

// Procesar el formulario cuando se envía
$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = floatval($_POST['precio'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $tipo_imagen = trim($_POST['tipo_imagen'] ?? '');

    // Validaciones
    $errores = [];

    if (empty($nombre)) {
        $errores[] = "El nombre del producto es obligatorio";
    }

    if (empty($descripcion)) {
        $errores[] = "La descripción del producto es obligatoria";
    }

    if ($precio <= 0) {
        $errores[] = "El precio debe ser mayor a 0";
    }

    if ($stock < 0) {
        $errores[] = "El stock no puede ser negativo";
    }

    if (empty($tipo_imagen)) {
        $errores[] = "El tipo de imagen es obligatorio";
    }

    // Procesar imagen si se subió
    $imagen = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $archivo_temporal = $_FILES['imagen']['tmp_name'];
        $nombre_archivo = $_FILES['imagen']['name'];
        $tipo_archivo = $_FILES['imagen']['type'];
        $tamano_archivo = $_FILES['imagen']['size'];

        // Validar tipo de archivo
        $tipos_permitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!in_array($tipo_archivo, $tipos_permitidos)) {
            $errores[] = "Solo se permiten archivos de imagen (JPG, PNG, GIF)";
        }

        // Validar tamaño (máximo 5MB)
        if ($tamano_archivo > 5 * 1024 * 1024) {
            $errores[] = "El archivo es demasiado grande. Máximo 5MB";
        }

        if (empty($errores)) {
            // Leer el contenido de la imagen
            $imagen = file_get_contents($archivo_temporal);
        }
    }

    // Si no hay errores, insertar en la base de datos
    if (empty($errores)) {
        $stmt = $conexion->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, imagen, tipo_imagen) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdiis", $nombre, $descripcion, $precio, $stock, $imagen, $tipo_imagen);

        if ($stmt->execute()) {
            $mensaje = "Producto agregado exitosamente";
            $tipo_mensaje = 'success';

            // Limpiar el formulario
            $_POST = array();
        } else {
            $mensaje = "Error al agregar el producto: " . $stmt->error;
            $tipo_mensaje = 'danger';
        }

        $stmt->close();
    } else {
        $mensaje = implode("<br>", $errores);
        $tipo_mensaje = 'danger';
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto - NUVIO</title>
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
            max-width: 800px;
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

        .form-control,
        .form-select {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
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
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
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

        .alert {
            border-radius: 12px;
            border: none;
            font-weight: 500;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
        }

        @media (max-width: 768px) {
            .admin-container {
                margin: 1rem;
                padding: 1rem;
            }

            .admin-title {
                font-size: 2rem;
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
                    <i class="fas fa-plus me-3"></i>
                    Agregar Producto
                </h1>
                <p class="admin-subtitle">Añade nuevos productos a tu tienda</p>
            </div>

            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                    <i class="fas fa-<?php echo $tipo_mensaje === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                    <?php echo $mensaje; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-box me-2"></i>
                        Información del Producto
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">
                                    <i class="fas fa-tag me-1"></i>Nombre del Producto
                                </label>
                                <input type="text" class="form-control" id="nombre" name="nombre"
                                    value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>"
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="precio" class="form-label">
                                    <i class="fas fa-dollar-sign me-1"></i>Precio
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="precio" name="precio"
                                        step="0.01" min="0"
                                        value="<?php echo htmlspecialchars($_POST['precio'] ?? ''); ?>"
                                        required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">
                                <i class="fas fa-align-left me-1"></i>Descripción
                            </label>
                            <textarea class="form-control" id="descripcion" name="descripcion"
                                rows="4" required><?php echo htmlspecialchars($_POST['descripcion'] ?? ''); ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="stock" class="form-label">
                                    <i class="fas fa-boxes me-1"></i>Stock
                                </label>
                                <input type="number" class="form-control" id="stock" name="stock"
                                    min="0"
                                    value="<?php echo htmlspecialchars($_POST['stock'] ?? '0'); ?>"
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="tipo_imagen" class="form-label">
                                    <i class="fas fa-file-image me-1"></i>Tipo de Imagen
                                </label>
                                <select class="form-select" id="tipo_imagen" name="tipo_imagen" required>
                                    <option value="">Seleccionar tipo...</option>
                                    <option value="image/jpeg" <?php echo ($_POST['tipo_imagen'] ?? '') === 'image/jpeg' ? 'selected' : ''; ?>>JPEG</option>
                                    <option value="image/jpg" <?php echo ($_POST['tipo_imagen'] ?? '') === 'image/jpg' ? 'selected' : ''; ?>>JPG</option>
                                    <option value="image/png" <?php echo ($_POST['tipo_imagen'] ?? '') === 'image/png' ? 'selected' : ''; ?>>PNG</option>
                                    <option value="image/gif" <?php echo ($_POST['tipo_imagen'] ?? '') === 'image/gif' ? 'selected' : ''; ?>>GIF</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="imagen" class="form-label">
                                <i class="fas fa-upload me-1"></i>Imagen del Producto
                            </label>
                            <input type="file" class="form-control" id="imagen" name="imagen"
                                accept="image/*">
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Formatos permitidos: JPG, PNG, GIF. Máximo 5MB.
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="admin_panel.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Volver al Panel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>
                                Guardar Producto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });

            // Auto-select image type based on file selection
            document.getElementById('imagen').addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const tipoImagen = document.getElementById('tipo_imagen');
                    tipoImagen.value = file.type;
                }
            });
        });
    </script>
</body>

</html>