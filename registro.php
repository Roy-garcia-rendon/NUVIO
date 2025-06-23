<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro-NUVIO</title>
</head>

<body>
    <?php
    include 'include/conexion.php';
    $db = new conexion();
    $conexion = $db->conex();
    $error = '';
    $success = '';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $contrasena = $_POST['contrasena'];
        $repite_contrasena = $_POST['repite_contrasena'];

        if ($contrasena !== $repite_contrasena) {
            $error = "Las contraseñas no coinciden";
        } else {
            // Verificar si el correo ya existe
            $stmt_check = $conexion->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt_check->bind_param("s", $email);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows > 0) {
                $error = "El correo electrónico ya está registrado.";
            } else {
                $contrasenaHash = password_hash($contrasena, PASSWORD_BCRYPT);

                $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, email, contrasena) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $nombre, $email, $contrasenaHash);

                if ($stmt->execute()) {
                    echo "<script>
                        alert('Registro exitoso');
                        window.location.href = 'login.php';
                      </script>";
                } else {
                    $error = "Error en el registro. Por favor, inténtalo de nuevo.";
                }

                $stmt->close();
            }
            $stmt_check->close();
        }
    }
    ?>

    <center>
        <h1>REGISTRO DE NUVIO</h1>
    </center>

    <?php if (!empty($error)) : ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="registro.php" method="post">
        <input type="text" name="nombre" placeholder="Nombre" required value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>">
        <input type="email" name="email" placeholder="Correo" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        <input type="password" name="contrasena" placeholder="Contraseña" required>
        <input type="password" name="repite_contrasena" placeholder="Repite la contraseña" required>
        <button type="submit">Registrarse</button>
    </form>

</body>

</html>