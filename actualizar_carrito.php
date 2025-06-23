<?php
session_start();

if (isset($_POST['id']) && isset($_POST['cantidad'])) {
    $id_producto = $_POST['id'];
    $nueva_cantidad = $_POST['cantidad'];

    // Verificar si el carrito existe
    if (isset($_SESSION['carrito'][$id_producto])) {
        // Actualizar la cantidad del producto
        if ($nueva_cantidad > 0) {
            $_SESSION['carrito'][$id_producto]['cantidad'] = $nueva_cantidad;
        } else {
            // Si la cantidad es 0, eliminar el producto del carrito
            unset($_SESSION['carrito'][$id_producto]);
        }
    }
}

header("Location: ver_carrito.php");
exit();
