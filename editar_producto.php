<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit();
}

include("include/conexion.php");
$db = new conexion();
$conexion = $db->conex();

// Verificar rol del usuario en la base de datos
$usuario_id = $_SESSION['usuario_id'];
$consultaRol = "SELECT tipo FROM usuarios WHERE id = ?";
$stmt = $conexion->prepare($consultaRol);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario || $usuario['tipo'] !== 'admin') {
    header('Location: index.php');
    exit();
}

// Obtener datos del producto
$id = $_GET["id"];
$resultado = mysqli_query($conexion, "SELECT * FROM productos WHERE id = $id");
$producto = mysqli_fetch_assoc($resultado);

// Actualizar producto si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $descripcion = $_POST["descripcion"];
    $precio = $_POST["precio"];
    $stock = $_POST["stock"];

    $sql = "UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=? WHERE id=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssdii", $nombre, $descripcion, $precio, $stock, $id);
    $stmt->execute();

    header("Location: admin_panel.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto - NUVIO</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
        }

        .edit-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            width: 100%;
            max-width: 600px;
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

        .edit-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .edit-title {
            color: #2c3e50;
            font-weight: 700;
            font-size: 2.5rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .edit-subtitle {
            color: #666;
            font-size: 1.1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-control {
            border: 2px solid #e1e5e9;
            border-radius: 12px;
            padding: 1rem 1rem 1rem 3rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
            width: 100%;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            transform: translateY(-2px);
            outline: none;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #667eea;
            z-index: 10;
        }

        .form-control:focus+.input-icon {
            color: #764ba2;
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        .btn {
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            padding: 1rem 2rem;
            font-size: 1.1rem;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .floating-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
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

        .product-preview {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 2px solid #e1e5e9;
        }

        .preview-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 1rem;
            text-align: center;
        }

        .preview-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .preview-item {
            background: white;
            padding: 1rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .preview-label {
            font-weight: 600;
            color: #667eea;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .preview-value {
            color: #2c3e50;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .edit-container {
                margin: 1rem;
                padding: 2rem;
            }

            .edit-title {
                font-size: 2rem;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
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

    <div class="edit-container">
        <div class="edit-header">
            <h1 class="edit-title">
                <i class="fas fa-edit me-3"></i>
                Editar Producto
            </h1>
            <p class="edit-subtitle">Modifica la información del producto</p>
        </div>

        <!-- Vista previa del producto actual -->
        <div class="product-preview">
            <h4 class="preview-title">
                <i class="fas fa-eye me-2"></i>
                Vista Previa Actual
            </h4>
            <div class="preview-content">
                <div class="preview-item">
                    <div class="preview-label">Nombre</div>
                    <div class="preview-value"><?php echo htmlspecialchars($producto['nombre']); ?></div>
                </div>
                <div class="preview-item">
                    <div class="preview-label">Precio</div>
                    <div class="preview-value">$<?php echo number_format($producto['precio'], 2); ?></div>
                </div>
                <div class="preview-item">
                    <div class="preview-label">Stock</div>
                    <div class="preview-value"><?php echo $producto['stock']; ?> unidades</div>
                </div>
            </div>
        </div>

        <form method="post" id="editForm">
            <div class="form-group">
                <label for="nombre" class="form-label">
                    <i class="fas fa-tag me-2"></i>
                    Nombre del Producto
                </label>
                <input type="text" class="form-control" id="nombre" name="nombre"
                    value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
                <i class="fas fa-tag input-icon"></i>
            </div>

            <div class="form-group">
                <label for="descripcion" class="form-label">
                    <i class="fas fa-align-left me-2"></i>
                    Descripción
                </label>
                <textarea class="form-control" id="descripcion" name="descripcion" required><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
                <i class="fas fa-align-left input-icon"></i>
            </div>

            <div class="form-group">
                <label for="precio" class="form-label">
                    <i class="fas fa-dollar-sign me-2"></i>
                    Precio
                </label>
                <input type="number" class="form-control" id="precio" name="precio"
                    step="0.01" min="0" value="<?php echo $producto['precio']; ?>" required>
                <i class="fas fa-dollar-sign input-icon"></i>
            </div>

            <div class="form-group">
                <label for="stock" class="form-label">
                    <i class="fas fa-boxes me-2"></i>
                    Stock
                </label>
                <input type="number" class="form-control" id="stock" name="stock"
                    min="0" value="<?php echo $producto['stock']; ?>" required>
                <i class="fas fa-boxes input-icon"></i>
            </div>

            <div class="action-buttons">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>
                    Actualizar Producto
                </button>
                <a href="admin_panel.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Cancelar
                </a>
                <a href="admin_panel.php?eliminar=<?php echo $producto['id']; ?>"
                    class="btn btn-danger"
                    onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?')">
                    <i class="fas fa-trash me-2"></i>
                    Eliminar
                </a>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Animación de entrada para los campos
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach((input, index) => {
                input.style.opacity = '0';
                input.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    input.style.transition = 'all 0.6s ease';
                    input.style.opacity = '1';
                    input.style.transform = 'translateY(0)';
                }, 200 * (index + 1));
            });

            // Efecto de focus mejorado
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.02)';
                });

                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1)';
                });
            });

            // Validación del formulario
            const form = document.getElementById('editForm');
            form.addEventListener('submit', function(e) {
                const nombre = document.getElementById('nombre').value.trim();
                const descripcion = document.getElementById('descripcion').value.trim();
                const precio = parseFloat(document.getElementById('precio').value);
                const stock = parseInt(document.getElementById('stock').value);

                if (!nombre || !descripcion) {
                    e.preventDefault();
                    alert('Por favor, completa todos los campos obligatorios');
                    return false;
                }

                if (precio < 0) {
                    e.preventDefault();
                    alert('El precio no puede ser negativo');
                    return false;
                }

                if (stock < 0) {
                    e.preventDefault();
                    alert('El stock no puede ser negativo');
                    return false;
                }
            });

            // Actualización en tiempo real de la vista previa
            const nombreInput = document.getElementById('nombre');
            const precioInput = document.getElementById('precio');
            const stockInput = document.getElementById('stock');

            nombreInput.addEventListener('input', function() {
                document.querySelector('.preview-content .preview-item:first-child .preview-value').textContent = this.value;
            });

            precioInput.addEventListener('input', function() {
                const precio = parseFloat(this.value) || 0;
                document.querySelector('.preview-content .preview-item:nth-child(2) .preview-value').textContent = '$' + precio.toFixed(2);
            });

            stockInput.addEventListener('input', function() {
                const stock = parseInt(this.value) || 0;
                document.querySelector('.preview-content .preview-item:nth-child(3) .preview-value').textContent = stock + ' unidades';
            });
        });
    </script>
</body>

</html>