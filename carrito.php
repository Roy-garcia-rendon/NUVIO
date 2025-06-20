<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NUVIO/carrito</title>
</head>

<body>
    <?php
    session_start(); // Inicia o continúa sesión
    include("include/conexion.php"); // Tu conexión

    // Verificar si hay un ID enviado por GET
    if (isset($_GET['agregar'])) {
        $id_producto = $_GET['agregar'];

        // Conexión a la BD
        $db = new conexion();
        $conexion = $db->conex();

        // Consultar el producto por ID
        $sql = "SELECT * FROM productos WHERE id = $id_producto";
        $resultado = mysqli_query($conexion, $sql);

        if (mysqli_num_rows($resultado) > 0) {
            $producto = mysqli_fetch_assoc($resultado);

            // Si el carrito no existe, lo creamos
            if (!isset($_SESSION['carrito'])) {
                $_SESSION['carrito'] = [];
            }

            // Si el producto ya está en el carrito, aumentamos la cantidad
            if (isset($_SESSION['carrito'][$id_producto])) {
                $_SESSION['carrito'][$id_producto]['cantidad'] += 1;
            } else {
                // Agregar producto al carrito
                $_SESSION['carrito'][$id_producto] = [
                    'nombre' => $producto['nombre'],
                    'precio' => $producto['precio'],
                    'cantidad' => 1
                ];
            }
        }

        mysqli_close($conexion);
    }

    // Redirigir de vuelta al inicio
    header("Location: home.php");
    exit;
    ?>

</body>

</html>