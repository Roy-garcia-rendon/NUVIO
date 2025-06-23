<?php
session_start();

//eliminar contenido del carrito
unset($_SESSION['carrito']);

//redirigir al carrito de nuevo
header("Location: ver_carrito.php");
exit();
