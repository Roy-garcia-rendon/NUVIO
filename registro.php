<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - NUVIO</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
        }

        .register-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            width: 100%;
            max-width: 450px;
            transform: translateY(20px);
            opacity: 0;
            animation: slideUp 0.8s ease forwards;
        }

        @keyframes slideUp {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .brand-logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-logo i {
            font-size: 3rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .register-title {
            text-align: center;
            color: #333;
            font-weight: 600;
            margin-bottom: 2rem;
            font-size: 1.8rem;
        }

        .form-floating {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-control {
            border: 2px solid #e1e5e9;
            border-radius: 12px;
            padding: 1rem 1rem 1rem 3rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            transform: translateY(-2px);
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #667eea;
            z-index: 10;
        }

        .btn-register {
            width: 100%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            border-radius: 12px;
            padding: 1rem;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-register:active {
            transform: translateY(-1px);
        }

        .btn-register::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-register:hover::before {
            left: 100%;
        }

        .alert {
            border-radius: 12px;
            border: none;
            margin-bottom: 1.5rem;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        .login-link {
            text-align: center;
            margin-top: 2rem;
            color: #666;
        }

        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
            color: #764ba2;
        }

        .password-strength {
            margin-top: 0.5rem;
            font-size: 0.85rem;
            color: #666;
        }

        .strength-bar {
            height: 4px;
            background: #e1e5e9;
            border-radius: 2px;
            margin-top: 0.5rem;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .floating-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(180deg);
            }
        }

        @media (max-width: 768px) {
            .register-container {
                margin: 1rem;
                padding: 2rem;
            }
        }
    </style>
</head>

<body>
    <!-- Floating background shapes -->
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="register-container">
        <div class="brand-logo">
            <i class="fas fa-user-plus"></i>
        </div>

        <h1 class="register-title">Únete a NUVIO</h1>

        <?php
        include 'include/conexion.php';
        $db = new conexion();
        $conexion = $db->conex();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = $_POST['nombre'];
            $email = $_POST['email'];
            $contrasena = $_POST['contrasena'];
            $repite_contrasena = $_POST['repite_contrasena'];

            if ($contrasena !== $repite_contrasena) {
                $error = "Las contraseñas no coinciden";
            } else {
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
                            Swal.fire({
                                title: '¡Registro exitoso!',
                                text: 'Tu cuenta ha sido creada correctamente',
                                icon: 'success',
                                confirmButtonText: 'Continuar'
                            }).then((result) => {
                                window.location.href = 'login.php';
                            });
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

        <?php if (!empty($error)) : ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="registro.php" method="post" id="registerForm">
            <div class="form-floating">
                <i class="fas fa-user input-icon"></i>
                <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre completo" required value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>">
            </div>

            <div class="form-floating">
                <i class="fas fa-envelope input-icon"></i>
                <input type="email" class="form-control" name="email" id="email" placeholder="Correo electrónico" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>

            <div class="form-floating">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" class="form-control" name="contrasena" id="password" placeholder="Contraseña" required>
                <div class="password-strength">
                    <div class="strength-bar">
                        <div class="strength-fill" id="strengthFill"></div>
                    </div>
                    <span id="strengthText">Fortaleza de la contraseña</span>
                </div>
            </div>

            <div class="form-floating">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" class="form-control" name="repite_contrasena" id="confirmPassword" placeholder="Confirmar contraseña" required>
            </div>

            <button type="submit" class="btn btn-register">
                <i class="fas fa-user-plus me-2"></i>
                Crear Cuenta
            </button>
        </form>

        <div class="login-link">
            ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Animación de entrada para los campos
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach((input, index) => {
                input.style.opacity = '0';
                input.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    input.style.transition = 'all 0.6s ease';
                    input.style.opacity = '1';
                    input.style.transform = 'translateY(0)';
                }, 200 * (index + 1));
            });

            // Efecto de focus mejorado
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.02)';
                });

                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1)';
                });
            });

            // Validación de contraseña en tiempo real
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirmPassword');
            const strengthFill = document.getElementById('strengthFill');
            const strengthText = document.getElementById('strengthText');

            password.addEventListener('input', function() {
                const strength = checkPasswordStrength(this.value);
                updateStrengthIndicator(strength);
            });

            function checkPasswordStrength(password) {
                let score = 0;
                if (password.length >= 8) score++;
                if (/[a-z]/.test(password)) score++;
                if (/[A-Z]/.test(password)) score++;
                if (/[0-9]/.test(password)) score++;
                if (/[^A-Za-z0-9]/.test(password)) score++;
                return score;
            }

            function updateStrengthIndicator(strength) {
                const colors = ['#ff4444', '#ff8800', '#ffbb33', '#00C851', '#007E33'];
                const texts = ['Muy débil', 'Débil', 'Media', 'Fuerte', 'Muy fuerte'];

                strengthFill.style.width = (strength * 20) + '%';
                strengthFill.style.backgroundColor = colors[strength - 1] || '#e1e5e9';
                strengthText.textContent = texts[strength - 1] || 'Fortaleza de la contraseña';
            }

            // Validación del formulario
            const form = document.getElementById('registerForm');
            form.addEventListener('submit', function(e) {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirmPassword').value;

                if (password !== confirmPassword) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Error',
                        text: 'Las contraseñas no coinciden',
                        icon: 'error',
                        confirmButtonText: 'Entendido'
                    });
                    return false;
                }

                if (password.length < 6) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Error',
                        text: 'La contraseña debe tener al menos 6 caracteres',
                        icon: 'error',
                        confirmButtonText: 'Entendido'
                    });
                    return false;
                }
            });
        });
    </script>
</body>

</html>