<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    session_start();

    echo "<h1>Tu carrito</h1>";

    if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
        echo "El carrito está vacío.";
    } else {
        foreach ($_SESSION['carrito'] as $id => $producto) {
            echo "<p>{$producto['nombre']} - Cantidad: {$producto['cantidad']} - Precio: {$producto['precio']}</p>";
        }
    }
    ?>

</body>

</html>