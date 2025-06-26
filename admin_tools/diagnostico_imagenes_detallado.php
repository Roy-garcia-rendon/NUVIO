<?php
// Diagnóstico detallado del sistema de imágenes
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html><head>";
echo "<title>🔍 Diagnóstico Detallado - Imágenes NUVIO</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }";
echo ".section { background: white; margin: 10px 0; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }";
echo ".success { border-left: 5px solid #28a745; }";
echo ".error { border-left: 5px solid #dc3545; }";
echo ".warning { border-left: 5px solid #ffc107; }";
echo ".info { border-left: 5px solid #17a2b8; }";
echo "pre { background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto; }";
echo ".test-link { display: inline-block; margin: 5px; padding: 10px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }";
echo ".test-link:hover { background: #0056b3; }";
echo "</style>";
echo "</head><body>";

echo "<h1>🔍 Diagnóstico Detallado - Sistema de Imágenes NUVIO</h1>";

// 1. Verificar conexión a base de datos
echo "<div class='section info'>";
echo "<h2>1. 🔌 Conexión a Base de Datos</h2>";

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nuvio";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo "<p class='error'>❌ Error de conexión: " . $conn->connect_error . "</p>";
    exit();
} else {
    echo "<p class='success'>✅ Conexión exitosa a la base de datos</p>";
    echo "<p>📊 Base de datos: $dbname</p>";
    echo "<p>🖥️ Servidor: $servername</p>";
}
echo "</div>";

// 2. Verificar estructura de la tabla productos
echo "<div class='section info'>";
echo "<h2>2. 📋 Estructura de la Tabla 'productos'</h2>";

$result = $conn->query("DESCRIBE productos");
if ($result) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Llave</th><th>Por defecto</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Verificar específicamente las columnas de imagen
    $check_imagen = $conn->query("SHOW COLUMNS FROM productos LIKE 'imagen'");
    $check_tipo = $conn->query("SHOW COLUMNS FROM productos LIKE 'tipo_imagen'");

    if ($check_imagen->num_rows > 0 && $check_tipo->num_rows > 0) {
        echo "<p class='success'>✅ Columnas 'imagen' y 'tipo_imagen' existen</p>";
    } else {
        echo "<p class='error'>❌ Faltan columnas de imagen</p>";
        if ($check_imagen->num_rows == 0) echo "<p>❌ Columna 'imagen' no existe</p>";
        if ($check_tipo->num_rows == 0) echo "<p>❌ Columna 'tipo_imagen' no existe</p>";
    }
} else {
    echo "<p class='error'>❌ Error al obtener estructura de la tabla</p>";
}
echo "</div>";

// 3. Verificar productos en la base de datos
echo "<div class='section info'>";
echo "<h2>3. 📦 Productos en la Base de Datos</h2>";

$result = $conn->query("SELECT id, nombre, precio, LENGTH(imagen) as tamano_imagen, tipo_imagen FROM productos ORDER BY id");
if ($result && $result->num_rows > 0) {
    echo "<p class='success'>✅ Se encontraron " . $result->num_rows . " productos</p>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Precio</th><th>Tamaño Imagen</th><th>Tipo Imagen</th><th>Prueba</th></tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
        echo "<td>$" . number_format($row['precio'], 2) . "</td>";
        echo "<td>" . ($row['tamano_imagen'] ? $row['tamano_imagen'] . ' bytes' : 'NULL') . "</td>";
        echo "<td>" . ($row['tipo_imagen'] ? $row['tipo_imagen'] : 'NULL') . "</td>";
        echo "<td><a href='mostrar_imagen.php?id=" . $row['id'] . "&debug=1' class='test-link' target='_blank'>🔍 Debug</a> ";
        echo "<a href='mostrar_imagen.php?id=" . $row['id'] . "' class='test-link' target='_blank'>🖼️ Ver</a></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='error'>❌ No se encontraron productos en la base de datos</p>";
}
echo "</div>";

// 4. Verificar archivos de imagen por defecto
echo "<div class='section info'>";
echo "<h2>4. 🖼️ Archivos de Imagen por Defecto</h2>";

$default_dir = 'C:/ProgramData/MySQL/MySQL Server 9.0/Uploads';
if (is_dir($default_dir)) {
    echo "<p class='success'>✅ Directorio existe: $default_dir</p>";

    $default_files = glob($default_dir . '/*.jpg');
    if (!empty($default_files)) {
        echo "<p class='success'>✅ Se encontraron " . count($default_files) . " archivos de imagen</p>";
        echo "<ul>";
        foreach ($default_files as $file) {
            $size = filesize($file);
            $readable = is_readable($file) ? "✅" : "❌";
            echo "<li>$readable " . basename($file) . " (" . number_format($size) . " bytes)</li>";
        }
        echo "</ul>";
    } else {
        echo "<p class='warning'>⚠️ No se encontraron archivos .jpg en el directorio</p>";
    }
} else {
    echo "<p class='error'>❌ El directorio no existe: $default_dir</p>";
}
echo "</div>";

// 5. Prueba directa de mostrar_imagen.php
echo "<div class='section info'>";
echo "<h2>5. 🧪 Pruebas Directas de mostrar_imagen.php</h2>";

// Obtener el primer producto para pruebas
$test_product = $conn->query("SELECT id FROM productos LIMIT 1");
if ($test_product && $test_product->num_rows > 0) {
    $product_id = $test_product->fetch_assoc()['id'];

    echo "<p>🔍 Probando con producto ID: $product_id</p>";

    // Prueba con debug
    echo "<a href='mostrar_imagen.php?id=$product_id&debug=1' class='test-link' target='_blank'>🔍 Debug con ID $product_id</a><br><br>";

    // Prueba sin debug
    echo "<a href='mostrar_imagen.php?id=$product_id' class='test-link' target='_blank'>🖼️ Ver imagen ID $product_id</a><br><br>";

    // Prueba con ID inválido
    echo "<a href='mostrar_imagen.php?id=999999&debug=1' class='test-link' target='_blank'>🔍 Debug con ID inválido</a><br><br>";

    // Prueba sin ID
    echo "<a href='mostrar_imagen.php?debug=1' class='test-link' target='_blank'>🔍 Debug sin ID</a><br><br>";
} else {
    echo "<p class='error'>❌ No hay productos para probar</p>";
}
echo "</div>";

// 6. Verificar configuración de PHP
echo "<div class='section info'>";
echo "<h2>6. ⚙️ Configuración de PHP</h2>";

echo "<h3>Extensiones de Imagen:</h3>";
$extensions = ['gd', 'imagick', 'exif'];
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p class='success'>✅ Extensión $ext está cargada</p>";
    } else {
        echo "<p class='warning'>⚠️ Extensión $ext no está cargada</p>";
    }
}

echo "<h3>Configuración de Memoria:</h3>";
echo "<p>📊 memory_limit: " . ini_get('memory_limit') . "</p>";
echo "<p>📊 max_execution_time: " . ini_get('max_execution_time') . "</p>";
echo "<p>📊 upload_max_filesize: " . ini_get('upload_max_filesize') . "</p>";
echo "<p>📊 post_max_size: " . ini_get('post_max_size') . "</p>";

echo "<h3>Headers HTTP:</h3>";
echo "<p>📊 output_buffering: " . ini_get('output_buffering') . "</p>";
echo "<p>📊 implicit_flush: " . ini_get('implicit_flush') . "</p>";
echo "</div>";

// 7. Verificar permisos y rutas
echo "<div class='section info'>";
echo "<h2>7. 🔐 Permisos y Rutas</h2>";

echo "<h3>Directorio Actual:</h3>";
echo "<p>📂 " . getcwd() . "</p>";

echo "<h3>Permisos de Archivos:</h3>";
$files_to_check = ['mostrar_imagen.php', 'index.php', 'include/conexion.php'];
foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        $perms = fileperms($file);
        $readable = is_readable($file) ? "✅" : "❌";
        echo "<p>$readable $file (permisos: " . substr(sprintf('%o', $perms), -4) . ")</p>";
    } else {
        echo "<p>❌ $file no existe</p>";
    }
}

echo "<h3>Permisos de Directorio Uploads:</h3>";
if (is_dir($default_dir)) {
    $perms = fileperms($default_dir);
    $readable = is_readable($default_dir) ? "✅" : "❌";
    $writable = is_writable($default_dir) ? "✅" : "❌";
    echo "<p>$readable Lectura: " . substr(sprintf('%o', $perms), -4) . "</p>";
    echo "<p>$writable Escritura: " . substr(sprintf('%o', $perms), -4) . "</p>";
} else {
    echo "<p>❌ Directorio no existe</p>";
}
echo "</div>";

// 8. Recomendaciones
echo "<div class='section warning'>";
echo "<h2>8. 💡 Recomendaciones</h2>";

echo "<h3>Si las imágenes no se muestran:</h3>";
echo "<ol>";
echo "<li>Verifica que los productos tienen imágenes BLOB en la base de datos</li>";
echo "<li>Ejecuta <a href='setup_imagenes.php'>setup_imagenes.php</a> para configurar la BD</li>";
echo "<li>Ejecuta <a href='agregar_productos_prueba.php'>agregar_productos_prueba.php</a> para agregar productos con imágenes</li>";
echo "<li>Verifica que la carpeta Uploads existe y tiene archivos .jpg</li>";
echo "<li>Revisa los logs de error de Apache/PHP</li>";
echo "<li>Prueba mostrar_imagen.php directamente con debug=1</li>";
echo "</ol>";

echo "<h3>Enlaces Útiles:</h3>";
echo "<p><a href='debug_simple.php' class='test-link'>🔍 Debug Simple</a> ";
echo "<a href='test_imagen.php' class='test-link'>🧪 Test Completo</a> ";
echo "<a href='index.php' class='test-link'>🏠 Página Principal</a></p>";
echo "</div>";

$conn->close();

echo "</body></html>";
