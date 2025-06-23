<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<?php
session_start();
include 'include/conexion.php';
$db = new conexion();
$conexion = $db->conex();
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $contrasena = $_POST["contrasena"];

    $stmt = $conexion->prepare("SELECT id, nombre, contrasena FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();


    if ($usuario = $resultado->fetch_assoc()) {
        if (password_verify($contrasena, $usuario["contrasena"])) {
            $_SESSION["usuario_id"] = $usuario["id"];
            $_SESSION["usuario_nombre"] = $usuario["nombre"];

            // Verificar o crear carrito para el usuario
            $usuario_id = $usuario['id'];
            $stmt_cart = $conexion->prepare("SELECT id FROM carritos WHERE usuario_id = ?");
            $stmt_cart->bind_param("i", $usuario_id);
            $stmt_cart->execute();
            $resultado_cart = $stmt_cart->get_result();

            if ($fila_cart = $resultado_cart->fetch_assoc()) {
                $_SESSION['carrito_id'] = $fila_cart['id'];
            } else {
                $stmt_insert = $conexion->prepare("INSERT INTO carritos (usuario_id) VALUES (?)");
                $stmt_insert->bind_param("i", $usuario_id);
                $stmt_insert->execute();
                $_SESSION['carrito_id'] = $stmt_insert->insert_id;
                $stmt_insert->close();
            }
            $stmt_cart->close();

            header("Location: index.php");
            exit();
        } else {
            $error = "Correo o contraseña incorrectos.";
        }
    } else {
        $error = "Correo o contraseña incorrectos.";
    }
    $stmt->close();
}
?>

<body>
    <h1>LOGIN DE NUVIO</h1>

    <?php if (!empty($error)) : ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="login.php" method="post">
        <input type="email" name="email" placeholder="Correo" required>
        <input type="password" name="contrasena" placeholder="Contraseña" required>
        <button type="submit">Iniciar Sesión</button>
    </form>

</body>

</html>