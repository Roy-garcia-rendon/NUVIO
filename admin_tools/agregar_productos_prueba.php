<?php
// Script para agregar productos de prueba con imágenes
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🛍️ Agregando Productos de Prueba</h2>";

// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nuvio";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    echo "❌ Error de conexión: " . $conn->connect_error;
    exit();
}

echo "✅ Conexión exitosa<br>";

// Verificar si las columnas existen
$result = $conn->query("SHOW COLUMNS FROM productos LIKE 'imagen'");
if ($result->num_rows == 0) {
    echo "❌ La columna 'imagen' no existe. Ejecuta setup_imagenes.php primero.<br>";
    exit();
}

// Productos de prueba
$productos = [
    [
        'nombre' => 'Camiseta Básica',
        'descripcion' => 'Camiseta de algodón 100% de alta calidad, perfecta para uso diario.',
        'precio' => 25.99,
        'stock' => 50
    ],
    [
        'nombre' => 'Pantalón Vaquero',
        'descripcion' => 'Pantalón vaquero clásico con corte moderno y máxima comodidad.',
        'precio' => 45.99,
        'stock' => 30
    ],
    [
        'nombre' => 'Zapatillas Deportivas',
        'descripcion' => 'Zapatillas ligeras y cómodas para deporte y uso casual.',
        'precio' => 79.99,
        'stock' => 25
    ],
    [
        'nombre' => 'Chaqueta de Cuero',
        'descripcion' => 'Chaqueta de cuero genuino con diseño elegante y duradero.',
        'precio' => 129.99,
        'stock' => 15
    ],
    [
        'nombre' => 'Reloj Elegante',
        'descripcion' => 'Reloj de pulsera con diseño minimalista y precisión suiza.',
        'precio' => 199.99,
        'stock' => 10
    ]
];

// Función para crear una imagen simple
function crearImagenSimple($texto, $color = [102, 126, 234])
{
    $width = 300;
    $height = 300;
    $image = imagecreate($width, $height);

    // Colores
    $white = imagecolorallocate($image, 255, 255, 255);
    $gray = imagecolorallocate($image, 200, 200, 200);
    $main_color = imagecolorallocate($image, $color[0], $color[1], $color[2]);

    // Fondo
    imagefill($image, 0, 0, $white);

    // Borde
    imagerectangle($image, 0, 0, $width - 1, $height - 1, $gray);

    // Texto
    $font_size = 5;
    $text_width = imagefontwidth($font_size) * strlen($texto);
    $text_height = imagefontheight($font_size);
    $x = ($width - $text_width) / 2;
    $y = ($height - $text_height) / 2;

    imagestring($image, $font_size, $x, $y, $texto, $main_color);

    // Capturar imagen como string
    ob_start();
    imagejpeg($image, null, 80);
    $image_data = ob_get_contents();
    ob_end_clean();

    imagedestroy($image);

    return $image_data;
}

// Colores para las imágenes
$colores = [
    [102, 126, 234], // Azul
    [40, 167, 69],   // Verde
    [220, 53, 69],   // Rojo
    [255, 193, 7],   // Amarillo
    [108, 117, 125]  // Gris
];

echo "<h3>📦 Agregando productos...</h3>";

$productos_agregados = 0;

foreach ($productos as $index => $producto) {
    // Crear imagen simple para el producto
    $imagen_data = crearImagenSimple($producto['nombre'], $colores[$index % count($colores)]);
    $tipo_imagen = 'image/jpeg';

    // Preparar consulta
    $sql = "INSERT INTO productos (nombre, descripcion, precio, stock, imagen, tipo_imagen) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param(
            "ssdiis",
            $producto['nombre'],
            $producto['descripcion'],
            $producto['precio'],
            $producto['stock'],
            $imagen_data,
            $tipo_imagen
        );

        if ($stmt->execute()) {
            echo "✅ " . $producto['nombre'] . " - Agregado exitosamente<br>";
            $productos_agregados++;
        } else {
            echo "❌ Error al agregar " . $producto['nombre'] . ": " . $stmt->error . "<br>";
        }

        $stmt->close();
    } else {
        echo "❌ Error en la preparación de la consulta para " . $producto['nombre'] . "<br>";
    }
}

$conn->close();

echo "<hr>";
echo "<h3>📊 Resumen:</h3>";
echo "Productos agregados: $productos_agregados de " . count($productos) . "<br>";

if ($productos_agregados > 0) {
    echo "<h3>🎯 Próximos pasos:</h3>";
    echo "<ol>";
    echo "<li><a href='index.php'>Ver productos en la página principal</a></li>";
    echo "<li><a href='debug_simple.php'>Verificar que las imágenes funcionan</a></li>";
    echo "<li><a href='test_imagen.php'>Ejecutar test completo</a></li>";
    echo "</ol>";

    echo "<h3>🔗 Enlaces de prueba:</h3>";
    echo "<ul>";
    for ($i = 1; $i <= min($productos_agregados, 3); $i++) {
        echo "<li><a href='mostrar_imagen.php?id=$i' target='_blank'>Imagen del producto $i</a></li>";
        echo "<li><a href='mostrar_imagen.php?id=$i&debug=1' target='_blank'>Debug del producto $i</a></li>";
    }
    echo "</ul>";
} else {
    echo "<h3>⚠️ No se pudieron agregar productos</h3>";
    echo "<p>Verifica que:</p>";
    echo "<ul>";
    echo "<li>La tabla productos existe</li>";
    echo "<li>Las columnas imagen y tipo_imagen existen</li>";
    echo "<li>Tienes permisos de escritura en la base de datos</li>";
    echo "</ul>";
    echo "<p><a href='setup_imagenes.php'>Ejecutar setup de imágenes</a></p>";
}
