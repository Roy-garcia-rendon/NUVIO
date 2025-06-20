<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NUVIO/home</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h1 class="mb-4 text-center">Lista de productos</h1>

        <?php
        // Conexión a la base de datos
        include("include/conexion.php");
        $db = new conexion();
        $conexion = $db->conex();

        // Consulta SQL
        $sql = "SELECT * FROM productos";
        $resultado = mysqli_query($conexion, $sql);
        ?>

        <!-- Contenedor de tarjetas -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
            <?php
            while ($row = mysqli_fetch_assoc($resultado)) {
            ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <!-- Imagen de producto (opcional) -->
                        <img src="ruta/a/una/imagen.jpg" class="card-img-top" alt="Imagen del producto">

                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row["nombre"]; ?></h5>
                            <p class="card-text"><?php echo $row["descripcion"]; ?></p>
                            <p class="card-text"><strong>Precio:</strong> $<?php echo $row["precio"]; ?></p>
                            <p class="card-text"><strong>Stock:</strong> <?php echo $row["stock"]; ?></p>
                        </div>

                        <div class="card-footer text-center">
                            <a href="carrito.php?agregar=<?php echo $row['id']; ?>">Agregar al carrito</a>

                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <?php
        // Cerrar conexión
        mysqli_close($conexion);
        ?>
    </div>

    <!-- Bootstrap JS) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>