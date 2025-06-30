<?php
session_start();
include("include/conexion.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_SESSION['usuario_id'], $_SESSION['carrito_id'])) {
    header("Location: index.php");
    exit();
}

$nombre = htmlspecialchars($_POST['nombre_cliente']);
$correo = htmlspecialchars($_POST['correo_cliente']);
$metodo = htmlspecialchars($_POST['metodo_pago']);
$total = floatval($_POST['total']);
$usuario_id = $_SESSION['usuario_id'];
$carrito_id = $_SESSION['carrito_id'];

$db = new conexion();
$conexion = $db->conex();

// 1. Insertar pedido
$stmt = $conexion->prepare("INSERT INTO pedidos (usuario_id, nombre_cliente, correo_cliente, metodo_pago, total) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("isssd", $usuario_id, $nombre, $correo, $metodo, $total);
$stmt->execute();
$pedido_id = $stmt->insert_id;
$stmt->close();

// 2. Obtener productos del carrito
$sql = "SELECT producto_id, cantidad FROM carrito_productos WHERE carrito_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $carrito_id);
$stmt->execute();
$resultado = $stmt->get_result();

while ($row = $resultado->fetch_assoc()) {
    $producto_id = $row['producto_id'];
    $cantidad = $row['cantidad'];

    // Obtener precio
    $res = $conexion->query("SELECT precio FROM productos WHERE id = $producto_id");
    $precio = $res->fetch_assoc()['precio'];

    // Insertar detalle
    $conexion->query("INSERT INTO pedido_detalles (pedido_id, producto_id, cantidad, precio_unitario) VALUES ($pedido_id, $producto_id, $cantidad, $precio)");
}
$stmt->close();

// 3. Vaciar carrito
$conexion->query("DELETE FROM carrito_productos WHERE carrito_id = $carrito_id");

// 4. Preparar correo (lo haremos en el paso siguiente)

// 5. Mostrar agradecimiento
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Compra finalizada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5 text-center">
        <h1>🎉 ¡Gracias por tu compra, <?= $nombre ?>!</h1>
        <p>Pedido procesado con éxito. Revisa tu correo: <strong><?= $correo ?></strong></p>
        <a href="index.php" class="btn btn-success mt-3">Volver a la tienda</a>
    </div>
</body>

</html>