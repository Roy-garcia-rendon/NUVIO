<?php
// Script para verificar y agregar imágenes a productos
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html><head>";
echo "<title>🖼️ Verificar y Agregar Imágenes - NUVIO</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }";
echo ".section { background: white; margin: 10px 0; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }";
echo ".success { border-left: 5px solid #28a745; }";
echo ".error { border-left: 5px solid #dc3545; }";
echo ".warning { border-left: 5px solid #ffc107; }";
echo ".info { border-left: 5px solid #17a2b8; }";
echo ".btn { display: inline-block; padding: 10px 20px; margin: 5px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; border: none; cursor: pointer; }";
echo ".btn:hover { background: #0056b3; }";
echo ".btn-success { background: #28a745; }";
echo ".btn-success:hover { background: #1e7e34; }";
echo ".btn-warning { background: #ffc107; color: #212529; }";
echo ".btn-warning:hover { background: #e0a800; }";
echo "</style>";
echo "</head><body>";

echo "<h1>🖼️ Verificar y Agregar Imágenes a Productos</h1>";

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nuvio";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo "<div class='section error'>";
    echo "<h2>❌ Error de Conexión</h2>";
    echo "<p>Error: " . $conn->connect_error . "</p>";
    echo "</div>";
    exit();
}

echo "<div class='section success'>";
echo "<h2>✅ Conexión Exitosa</h2>";
echo "<p>Conectado a la base de datos: $dbname</p>";
echo "</div>";

// Verificar estructura de la tabla
echo "<div class='section info'>";
echo "<h2>📋 Verificando Estructura de la Tabla</h2>";

$check_imagen = $conn->query("SHOW COLUMNS FROM productos LIKE 'imagen'");
$check_tipo = $conn->query("SHOW COLUMNS FROM productos LIKE 'tipo_imagen'");

if ($check_imagen->num_rows == 0 || $check_tipo->num_rows == 0) {
    echo "<p class='error'>❌ Faltan columnas de imagen</p>";

    if ($check_imagen->num_rows == 0) {
        echo "<p>❌ Columna 'imagen' no existe</p>";
    }
    if ($check_tipo->num_rows == 0) {
        echo "<p>❌ Columna 'tipo_imagen' no existe</p>";
    }

    echo "<form method='post'>";
    echo "<input type='submit' name='crear_columnas' value='Crear Columnas de Imagen' class='btn btn-warning'>";
    echo "</form>";
} else {
    echo "<p class='success'>✅ Columnas 'imagen' y 'tipo_imagen' existen</p>";
}
echo "</div>";

// Crear columnas si se solicita
if (isset($_POST['crear_columnas'])) {
    echo "<div class='section info'>";
    echo "<h2>🔧 Creando Columnas de Imagen</h2>";

    $sql_imagen = "ALTER TABLE productos ADD COLUMN imagen LONGBLOB";
    $sql_tipo = "ALTER TABLE productos ADD COLUMN tipo_imagen VARCHAR(100)";

    if ($conn->query($sql_imagen)) {
        echo "<p class='success'>✅ Columna 'imagen' creada</p>";
    } else {
        echo "<p class='error'>❌ Error al crear columna 'imagen': " . $conn->error . "</p>";
    }

    if ($conn->query($sql_tipo)) {
        echo "<p class='success'>✅ Columna 'tipo_imagen' creada</p>";
    } else {
        echo "<p class='error'>❌ Error al crear columna 'tipo_imagen': " . $conn->error . "</p>";
    }

    echo "</div>";
}

// Verificar productos existentes
echo "<div class='section info'>";
echo "<h2>📦 Verificando Productos Existentes</h2>";

$result = $conn->query("SELECT id, nombre, LENGTH(imagen) as tamano_imagen, tipo_imagen FROM productos ORDER BY id");
if ($result && $result->num_rows > 0) {
    echo "<p>Se encontraron " . $result->num_rows . " productos:</p>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Tamaño Imagen</th><th>Tipo Imagen</th><th>Estado</th></tr>";

    $productos_sin_imagen = [];

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
        echo "<td>" . ($row['tamano_imagen'] ? $row['tamano_imagen'] . ' bytes' : 'NULL') . "</td>";
        echo "<td>" . ($row['tipo_imagen'] ? $row['tipo_imagen'] : 'NULL') . "</td>";

        if ($row['tamano_imagen'] && $row['tipo_imagen']) {
            echo "<td class='success'>✅ Con imagen</td>";
        } else {
            echo "<td class='error'>❌ Sin imagen</td>";
            $productos_sin_imagen[] = $row['id'];
        }
        echo "</tr>";
    }
    echo "</table>";

    if (!empty($productos_sin_imagen)) {
        echo "<p class='warning'>⚠️ " . count($productos_sin_imagen) . " productos sin imagen</p>";
        echo "<form method='post'>";
        echo "<input type='hidden' name='productos_sin_imagen' value='" . implode(',', $productos_sin_imagen) . "'>";
        echo "<input type='submit' name='agregar_imagenes' value='Agregar Imágenes a Productos Existentes' class='btn btn-warning'>";
        echo "</form>";
    }
} else {
    echo "<p class='error'>❌ No se encontraron productos</p>";
    echo "<form method='post'>";
    echo "<input type='submit' name='crear_productos' value='Crear Productos de Prueba' class='btn btn-success'>";
    echo "</form>";
}
echo "</div>";

// Crear productos de prueba
if (isset($_POST['crear_productos'])) {
    echo "<div class='section info'>";
    echo "<h2>📦 Creando Productos de Prueba</h2>";

    $productos_prueba = [
        ['nombre' => 'Camiseta Básica', 'descripcion' => 'Camiseta de algodón 100%', 'precio' => 25.99, 'stock' => 50],
        ['nombre' => 'Pantalón Vaquero', 'descripcion' => 'Pantalón vaquero clásico', 'precio' => 45.99, 'stock' => 30],
        ['nombre' => 'Zapatillas Deportivas', 'descripcion' => 'Zapatillas para running', 'precio' => 79.99, 'stock' => 25],
        ['nombre' => 'Chaqueta de Cuero', 'descripcion' => 'Chaqueta de cuero genuino', 'precio' => 129.99, 'stock' => 15],
        ['nombre' => 'Reloj Elegante', 'descripcion' => 'Reloj de pulsera elegante', 'precio' => 89.99, 'stock' => 20]
    ];

    $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, imagen, tipo_imagen) VALUES (?, ?, ?, ?, ?, ?)");

    foreach ($productos_prueba as $producto) {
        // Crear imagen simple para cada producto
        $width = 300;
        $height = 300;
        $image = imagecreate($width, $height);

        // Colores
        $white = imagecolorallocate($image, 255, 255, 255);
        $gray = imagecolorallocate($image, 200, 200, 200);
        $blue = imagecolorallocate($image, 102, 126, 234);
        $red = imagecolorallocate($image, 220, 53, 69);
        $green = imagecolorallocate($image, 40, 167, 69);

        // Fondo
        imagefill($image, 0, 0, $white);

        // Borde
        imagerectangle($image, 0, 0, $width - 1, $height - 1, $gray);

        // Texto del producto
        $texto = $producto['nombre'];
        $font_size = 5;
        $text_width = imagefontwidth($font_size) * strlen($texto);
        $text_height = imagefontheight($font_size);
        $x = ($width - $text_width) / 2;
        $y = ($height - $text_height) / 2;

        imagestring($image, $font_size, $x, $y, $texto, $blue);

        // Precio
        $precio_texto = '$' . number_format($producto['precio'], 2);
        $precio_x = ($width - imagefontwidth($font_size) * strlen($precio_texto)) / 2;
        $precio_y = $y + $text_height + 20;
        imagestring($image, $font_size, $precio_x, $precio_y, $precio_texto, $red);

        // Capturar imagen como string
        ob_start();
        imagejpeg($image, null, 80);
        $imagen_data = ob_get_contents();
        ob_end_clean();

        $tipo_imagen = 'image/jpeg';

        $stmt->bind_param("ssdis", $producto['nombre'], $producto['descripcion'], $producto['precio'], $producto['stock'], $imagen_data, $tipo_imagen);

        if ($stmt->execute()) {
            echo "<p class='success'>✅ Producto creado: " . $producto['nombre'] . "</p>";
        } else {
            echo "<p class='error'>❌ Error al crear producto: " . $producto['nombre'] . " - " . $stmt->error . "</p>";
        }

        imagedestroy($image);
    }

    $stmt->close();
    echo "</div>";
}

// Agregar imágenes a productos existentes
if (isset($_POST['agregar_imagenes'])) {
    echo "<div class='section info'>";
    echo "<h2>🖼️ Agregando Imágenes a Productos Existentes</h2>";

    $productos_ids = explode(',', $_POST['productos_sin_imagen']);
    $stmt = $conn->prepare("UPDATE productos SET imagen = ?, tipo_imagen = ? WHERE id = ?");

    foreach ($productos_ids as $id) {
        $id = (int)$id;

        // Obtener información del producto
        $producto_info = $conn->query("SELECT nombre, precio FROM productos WHERE id = $id")->fetch_assoc();

        // Crear imagen personalizada
        $width = 300;
        $height = 300;
        $image = imagecreate($width, $height);

        // Colores
        $white = imagecolorallocate($image, 255, 255, 255);
        $gray = imagecolorallocate($image, 200, 200, 200);
        $blue = imagecolorallocate($image, 102, 126, 234);
        $red = imagecolorallocate($image, 220, 53, 69);

        // Fondo
        imagefill($image, 0, 0, $white);

        // Borde
        imagerectangle($image, 0, 0, $width - 1, $height - 1, $gray);

        // Texto del producto
        $texto = $producto_info['nombre'];
        $font_size = 5;
        $text_width = imagefontwidth($font_size) * strlen($texto);
        $text_height = imagefontheight($font_size);
        $x = ($width - $text_width) / 2;
        $y = ($height - $text_height) / 2;

        imagestring($image, $font_size, $x, $y, $texto, $blue);

        // Precio
        $precio_texto = '$' . number_format($producto_info['precio'], 2);
        $precio_x = ($width - imagefontwidth($font_size) * strlen($precio_texto)) / 2;
        $precio_y = $y + $text_height + 20;
        imagestring($image, $font_size, $precio_x, $precio_y, $precio_texto, $red);

        // Capturar imagen como string
        ob_start();
        imagejpeg($image, null, 80);
        $imagen_data = ob_get_contents();
        ob_end_clean();

        $tipo_imagen = 'image/jpeg';

        $stmt->bind_param("ssi", $imagen_data, $tipo_imagen, $id);

        if ($stmt->execute()) {
            echo "<p class='success'>✅ Imagen agregada al producto ID $id: " . $producto_info['nombre'] . "</p>";
        } else {
            echo "<p class='error'>❌ Error al agregar imagen al producto ID $id: " . $stmt->error . "</p>";
        }

        imagedestroy($image);
    }

    $stmt->close();
    echo "</div>";
}

// Verificar resultado final
echo "<div class='section success'>";
echo "<h2>🎯 Verificación Final</h2>";

$result_final = $conn->query("SELECT COUNT(*) as total, COUNT(imagen) as con_imagen FROM productos");
$stats = $result_final->fetch_assoc();

echo "<p>📊 Total de productos: " . $stats['total'] . "</p>";
echo "<p>🖼️ Productos con imagen: " . $stats['con_imagen'] . "</p>";
echo "<p>❌ Productos sin imagen: " . ($stats['total'] - $stats['con_imagen']) . "</p>";

if ($stats['con_imagen'] > 0) {
    echo "<p class='success'>✅ Hay productos con imágenes disponibles</p>";
    echo "<a href='index.php' class='btn btn-success'>Ver Página Principal</a> ";
    echo "<a href='diagnostico_imagenes_detallado.php' class='btn btn-info'>Diagnóstico Detallado</a>";
} else {
    echo "<p class='error'>❌ No hay productos con imágenes</p>";
}
echo "</div>";

$conn->close();

echo "</body></html>";
