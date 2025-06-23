<?php
session_start();
require 'include/conexion.php';

// Crear conexión
$db = new conexion();
$conexion = $db->conex();

if (isset($_SESSION['carrito_id'])) {
    $carrito_id = $_SESSION['carrito_id'];

    // Eliminar todos los productos del carrito en la base de datos
    $stmt = $conexion->prepare("DELETE FROM carrito_productos WHERE carrito_id = ?");
    $stmt->bind_param("i", $carrito_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: ver_carrito.php");
exit();
