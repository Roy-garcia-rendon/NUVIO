<?php
session_start();
require 'include/conexion.php';

// Crear conexión
$db = new conexion();
$conexion = $db->conex();

if (isset($_POST['id']) && isset($_SESSION['carrito_id'])) {
    $producto_id = $_POST['id'];
    $carrito_id = $_SESSION['carrito_id'];

    // Eliminar el producto de la base de datos (carrito_productos)
    $stmt = $conexion->prepare("DELETE FROM carrito_productos WHERE carrito_id = ? AND producto_id = ?");
    $stmt->bind_param("ii", $carrito_id, $producto_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: ver_carrito.php");
exit();
