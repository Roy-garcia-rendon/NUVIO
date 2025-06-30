<?php
session_start();
if (!isset($_POST['total'])) {
    header("Location: index.php");
    exit();
}

$total = $_POST['total'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Pago simulado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Simulación de pago</h2>

        <form action="confirmacion.php" method="post">
            <input type="hidden" name="total" value="<?= $total ?>">

            <div class="mb-3">
                <label class="form-label">Nombre completo:</label>
                <input type="text" class="form-control" name="nombre_cliente" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Correo electrónico:</label>
                <input type="email" class="form-control" name="correo_cliente" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Método de pago:</label>
                <select class="form-select" name="metodo_pago" required>
                    <option value="tarjeta">Tarjeta de crédito</option>
                    <option value="paypal">PayPal</option>
                    <option value="oxxo">Pago en OXXO</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Finalizar compra</button>
        </form>
    </div>
</body>

</html>