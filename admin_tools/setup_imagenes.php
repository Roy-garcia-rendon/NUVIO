<?php
// Script para configurar la base de datos para imágenes BLOB
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔧 Configuración de Base de Datos para Imágenes</h2>";

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

echo "✅ Conexión exitosa a la base de datos: $dbname<br><br>";

// Verificar si la tabla productos existe
$result = $conn->query("SHOW TABLES LIKE 'productos'");
if ($result->num_rows == 0) {
    echo "❌ La tabla 'productos' no existe<br>";
    echo "💡 Primero crea la tabla productos con los campos básicos<br>";
    exit();
}

echo "✅ Tabla 'productos' existe<br>";

// Verificar columnas existentes
$result = $conn->query("DESCRIBE productos");
$columnas_existentes = [];
while ($row = $result->fetch_assoc()) {
    $columnas_existentes[] = $row['Field'];
}

echo "<h3>📋 Columnas existentes:</h3>";
echo "<ul>";
foreach ($columnas_existentes as $columna) {
    echo "<li>$columna</li>";
}
echo "</ul>";

// Verificar si las columnas de imagen existen
$tiene_imagen = in_array('imagen', $columnas_existentes);
$tiene_tipo_imagen = in_array('tipo_imagen', $columnas_existentes);

echo "<h3>🔍 Estado de columnas de imagen:</h3>";
echo "📦 Columna 'imagen': " . ($tiene_imagen ? "✅ Existe" : "❌ No existe") . "<br>";
echo "🏷️ Columna 'tipo_imagen': " . ($tiene_tipo_imagen ? "✅ Existe" : "❌ No existe") . "<br>";

// Agregar columnas si no existen
if (!$tiene_imagen || !$tiene_tipo_imagen) {
    echo "<h3>🔧 Agregando columnas faltantes...</h3>";

    if (!$tiene_imagen) {
        $sql = "ALTER TABLE productos ADD COLUMN imagen LONGBLOB";
        if ($conn->query($sql) === TRUE) {
            echo "✅ Columna 'imagen' agregada exitosamente<br>";
        } else {
            echo "❌ Error al agregar columna 'imagen': " . $conn->error . "<br>";
        }
    }

    if (!$tiene_tipo_imagen) {
        $sql = "ALTER TABLE productos ADD COLUMN tipo_imagen VARCHAR(100)";
        if ($conn->query($sql) === TRUE) {
            echo "✅ Columna 'tipo_imagen' agregada exitosamente<br>";
        } else {
            echo "❌ Error al agregar columna 'tipo_imagen': " . $conn->error . "<br>";
        }
    }
} else {
    echo "<h3>✅ Todas las columnas necesarias ya existen</h3>";
}

// Crear carpeta media si no existe
echo "<h3>📁 Verificando carpeta media...</h3>";
if (!is_dir('media')) {
    if (mkdir('media', 0755, true)) {
        echo "✅ Carpeta 'media' creada exitosamente<br>";
    } else {
        echo "❌ Error al crear carpeta 'media'<br>";
    }
} else {
    echo "✅ Carpeta 'media' ya existe<br>";
}

// Crear imagen por defecto si no existe
echo "<h3>🖼️ Verificando imagen por defecto...</h3>";
$default_dir = 'C:/ProgramData/MySQL/MySQL Server 9.0/Uploads';
$default_files = glob($default_dir . '/*.jpg');

if (empty($default_files)) {
    echo "⚠️ No se encontraron imágenes por defecto en: $default_dir<br>";
    echo "💡 Creando imágenes por defecto en la carpeta Uploads<br>";

    // Crear el directorio si no existe
    if (!is_dir($default_dir)) {
        if (mkdir($default_dir, 0755, true)) {
            echo "✅ Directorio creado exitosamente<br>";
        } else {
            echo "❌ Error al crear el directorio<br>";
            exit();
        }
    }

    // Crear múltiples imágenes por defecto
    $imagenes_por_defecto = [
        'default.jpg' => 'Imagen por defecto',
        'no-image.jpg' => 'Sin imagen',
        'placeholder.jpg' => 'Placeholder'
    ];

    foreach ($imagenes_por_defecto as $filename => $texto) {
        $ruta_completa = $default_dir . '/' . $filename;

        // Crear una imagen simple
        $width = 300;
        $height = 300;
        $image = imagecreate($width, $height);

        // Colores
        $white = imagecolorallocate($image, 255, 255, 255);
        $gray = imagecolorallocate($image, 200, 200, 200);
        $blue = imagecolorallocate($image, 102, 126, 234);

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

        imagestring($image, $font_size, $x, $y, $texto, $blue);

        // Guardar imagen
        if (imagejpeg($image, $ruta_completa, 80)) {
            echo "✅ Imagen creada: $filename<br>";
        } else {
            echo "❌ Error al crear: $filename<br>";
        }

        imagedestroy($image);
    }
} else {
    echo "✅ Imágenes por defecto encontradas en: $default_dir<br>";
    echo "📏 Archivos disponibles: " . count($default_files) . "<br>";
    foreach ($default_files as $file) {
        echo "📁 " . basename($file) . " (" . filesize($file) . " bytes)<br>";
    }
}

// Mostrar productos existentes
echo "<h3>📊 Productos en la base de datos:</h3>";
$result = $conn->query("SELECT id, nombre, LENGTH(imagen) as tamano_imagen, tipo_imagen FROM productos LIMIT 10");
if ($result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Tamaño Imagen</th><th>Tipo Imagen</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
        echo "<td>" . ($row['tamano_imagen'] ? $row['tamano_imagen'] . ' bytes' : 'NULL') . "</td>";
        echo "<td>" . ($row['tipo_imagen'] ? $row['tipo_imagen'] : 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "⚠️ No hay productos en la base de datos<br>";
}

$conn->close();

echo "<hr>";
echo "<h3>🎯 Próximos pasos:</h3>";
echo "<ol>";
echo "<li>Si no hay productos, agrega algunos productos a la base de datos</li>";
echo "<li>Para agregar imágenes a productos existentes, usa el panel de administración</li>";
echo "<li>Prueba el sistema visitando: <a href='index.php'>index.php</a></li>";
echo "<li>Para debug, visita: <a href='test_imagen.php'>test_imagen.php</a></li>";
echo "</ol>";

echo "<h3>🔗 Enlaces útiles:</h3>";
echo "<ul>";
echo "<li><a href='index.php'>🏠 Página principal</a></li>";
echo "<li><a href='admin_panel.php'>⚙️ Panel de administración</a></li>";
echo "<li><a href='test_imagen.php'>🔍 Test de imágenes</a></li>";
echo "</ul>";
