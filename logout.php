<?php
session_start();
session_unset(); // Limpiar variables de sesión
session_destroy(); // Destruir la sesión
header("Location: index.php");
exit();
