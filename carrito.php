<?php
session_start();
include("include/conexion.php"); // Tu clase de conexión

// Verificar si hay un ID enviado por GET
if (isset($_GET['agregar']) && isset($_SESSION['carrito_id'])) {
    $producto_id = (int) $_GET['agregar'];
    $carrito_id = (int) $_SESSION['carrito_id'];

    // Conexión a la BD
    $db = new conexion();
    $conexion = $db->conex();

    // Verificar si el producto existe
    $sql_producto = "SELECT * FROM productos WHERE id = $producto_id";
    $resultado = mysqli_query($conexion, $sql_producto);

    if (mysqli_num_rows($resultado) > 0) {
        // Verificar si ya existe ese producto en el carrito
        $sql_check = "SELECT * FROM carrito_productos WHERE carrito_id = $carrito_id AND producto_id = $producto_id";
        $resultado_check = mysqli_query($conexion, $sql_check);

        if (mysqli_num_rows($resultado_check) > 0) {
            // Ya está en el carrito, actualizar cantidad
            $sql_update = "UPDATE carrito_productos SET cantidad = cantidad + 1 WHERE carrito_id = $carrito_id AND producto_id = $producto_id";
            mysqli_query($conexion, $sql_update);
        } else {
            // No está en el carrito, insertar nuevo registro
            $sql_insert = "INSERT INTO carrito_productos (carrito_id, producto_id, cantidad) VALUES ($carrito_id, $producto_id, 1)";
            mysqli_query($conexion, $sql_insert);
        }
    }

    mysqli_close($conexion);
}

// Redirigir de vuelta al inicio
header("Location: index.php");
exit;
