<?php
session_start();

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Eliminar el producto del carrito si existe
    if (isset($_SESSION['carrito'][$id])) {
        unset($_SESSION['carrito'][$id]);
    }
}

// Redirigir de vuelta al carrito
header("Location: carrito.php");
exit();
