<?php
session_start();
require 'include/conexion.php';

// Verificar que el usuario tenga un carrito activo
if (!isset($_SESSION['carrito_id'])) {
    header("Location: ver_carrito.php");
    exit();
}

// Verificar que se recibieron los datos necesarios
if (isset($_POST['id']) && isset($_POST['cantidad'])) {
    $producto_id = (int)$_POST['id'];
    $nueva_cantidad = (int)$_POST['cantidad'];
    $carrito_id = $_SESSION['carrito_id'];

    // Crear conexión
    $db = new conexion();
    $conexion = $db->conex();

    // Validar que la cantidad sea mayor a 0
    if ($nueva_cantidad > 0) {
        // Verificar si el producto existe en el carrito
        $stmt = $conexion->prepare("
            SELECT id FROM carrito_productos 
            WHERE carrito_id = ? AND producto_id = ?
        ");
        $stmt->bind_param("ii", $carrito_id, $producto_id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            // Actualizar la cantidad en la base de datos
            $stmt = $conexion->prepare("
                UPDATE carrito_productos 
                SET cantidad = ? 
                WHERE carrito_id = ? AND producto_id = ?
            ");
            $stmt->bind_param("iii", $nueva_cantidad, $carrito_id, $producto_id);

            if ($stmt->execute()) {
                // Éxito - redirigir de vuelta al carrito
                header("Location: ver_carrito.php?success=1");
            } else {
                // Error en la actualización
                header("Location: ver_carrito.php?error=update");
            }
        } else {
            // El producto no existe en el carrito
            header("Location: ver_carrito.php?error=not_found");
        }
    } else {
        // Si la cantidad es 0 o menor, eliminar el producto del carrito
        $stmt = $conexion->prepare("
            DELETE FROM carrito_productos 
            WHERE carrito_id = ? AND producto_id = ?
        ");
        $stmt->bind_param("ii", $carrito_id, $producto_id);

        if ($stmt->execute()) {
            header("Location: ver_carrito.php?success=removed");
        } else {
            header("Location: ver_carrito.php?error=delete");
        }
    }

    $stmt->close();
    $conexion->close();
} else {
    // Datos faltantes
    header("Location: ver_carrito.php?error=missing_data");
}

exit();
