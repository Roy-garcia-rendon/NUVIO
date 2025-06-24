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
    <title>Editar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <form method="post" class="container mt-4">
        <h3>Editar Producto</h3>
        <input class="form-control mb-2" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
        <textarea class="form-control mb-2" name="descripcion" required><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
        <input class="form-control mb-2" type="number" name="precio" step="0.01" value="<?php echo $producto['precio']; ?>" required>
        <input class="form-control mb-2" type="number" name="stock" value="<?php echo $producto['stock']; ?>" required>
        <button class="btn btn-primary" type="submit">Actualizar</button>
        <a class="btn btn-secondary" href="admin_panel.php">Cancelar</a>
    </form>
</body>

</html>