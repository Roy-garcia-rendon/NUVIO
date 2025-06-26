<?php
// Debug simple para mostrar_imagen.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Debug Simple - mostrar_imagen.php</h1>";

// 1. Probar conexión directa
echo "<h2>1. Prueba de conexión directa</h2>";
$conn = new mysqli("localhost", "root", "", "nuvio");

if ($conn->connect_error) {
    echo "❌ Error de conexión: " . $conn->connect_error;
    exit();
}
echo "✅ Conexión exitosa<br>";

// 2. Verificar tabla productos
echo "<h2>2. Verificar tabla productos</h2>";
$result = $conn->query("SHOW TABLES LIKE 'productos'");
if ($result->num_rows > 0) {
    echo "✅ Tabla productos existe<br>";
} else {
    echo "❌ Tabla productos NO existe<br>";
    exit();
}

// 3. Verificar columnas
echo "<h2>3. Verificar columnas</h2>";
$result = $conn->query("DESCRIBE productos");
$columnas = [];
while ($row = $result->fetch_assoc()) {
    $columnas[] = $row['Field'];
    echo "📋 " . $row['Field'] . " - " . $row['Type'] . "<br>";
}

$tiene_imagen = in_array('imagen', $columnas);
$tiene_tipo_imagen = in_array('tipo_imagen', $columnas);

echo "<br>";
echo "📦 Columna 'imagen': " . ($tiene_imagen ? "✅" : "❌") . "<br>";
echo "🏷️ Columna 'tipo_imagen': " . ($tiene_tipo_imagen ? "✅" : "❌") . "<br>";

// 4. Verificar productos
echo "<h2>4. Verificar productos</h2>";
$result = $conn->query("SELECT COUNT(*) as total FROM productos");
$row = $result->fetch_assoc();
echo "📊 Total productos: " . $row['total'] . "<br>";

if ($row['total'] > 0) {
    $result = $conn->query("SELECT id, nombre, LENGTH(imagen) as tamano FROM productos LIMIT 3");
    echo "<h3>Primeros 3 productos:</h3>";
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . " - " . $row['nombre'] . " - Imagen: " . ($row['tamano'] ? $row['tamano'] . " bytes" : "NULL") . "<br>";
    }
}

// 5. Probar mostrar_imagen.php directamente
echo "<h2>5. Probar mostrar_imagen.php</h2>";
if ($row['total'] > 0) {
    $result = $conn->query("SELECT id FROM productos LIMIT 1");
    $row = $result->fetch_assoc();
    $test_id = $row['id'];

    echo "🧪 Probando con ID: $test_id<br>";
    echo "🔗 <a href='mostrar_imagen.php?id=$test_id' target='_blank'>mostrar_imagen.php?id=$test_id</a><br>";
    echo "🔗 <a href='mostrar_imagen.php?id=$test_id&debug=1' target='_blank'>mostrar_imagen.php?id=$test_id&debug=1</a><br>";

    // Probar la consulta directamente
    $stmt = $conn->prepare("SELECT imagen, tipo_imagen FROM productos WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $test_id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($fila = $resultado->fetch_assoc()) {
            echo "✅ Producto encontrado<br>";
            echo "📦 Tiene imagen: " . ($fila['imagen'] ? 'SÍ (' . strlen($fila['imagen']) . ' bytes)' : 'NO') . "<br>";
            echo "🏷️ Tipo: " . ($fila['tipo_imagen'] ? $fila['tipo_imagen'] : 'NULL') . "<br>";
        } else {
            echo "❌ Producto no encontrado<br>";
        }
        $stmt->close();
    }
}

// 6. Verificar archivo de imagen por defecto
echo "<h3>6. Verificando archivo de imagen por defecto...</h3>";
$default_dir = 'C:/ProgramData/MySQL/MySQL Server 9.0/Uploads';
$default_files = glob($default_dir . '/*.jpg');
if (!empty($default_files)) {
    echo "✅ Archivos de imagen por defecto encontrados en: $default_dir<br>";
    echo "📏 Archivos disponibles: " . count($default_files) . "<br>";
    foreach ($default_files as $file) {
        echo "📁 " . basename($file) . " (" . filesize($file) . " bytes)<br>";
    }
} else {
    echo "❌ No se encontraron archivos de imagen en: $default_dir<br>";
}

$conn->close();

echo "<hr>";
echo "<h2>🎯 Acciones recomendadas:</h2>";
echo "<ol>";
if (!$tiene_imagen || !$tiene_tipo_imagen) {
    echo "<li><strong>Ejecutar setup_imagenes.php</strong> para agregar las columnas faltantes</li>";
}
if ($row['total'] == 0) {
    echo "<li><strong>Agregar productos</strong> a la base de datos</li>";
}
if (empty($default_files)) {
    echo "<li><strong>Crear imágenes por defecto</strong> en $default_dir</li>";
}
echo "<li><strong>Probar mostrar_imagen.php</strong> con debug=1</li>";
echo "</ol>";

echo "<h2>🔗 Enlaces útiles:</h2>";
echo "<ul>";
echo "<li><a href='setup_imagenes.php'>🔧 Setup de imágenes</a></li>";
echo "<li><a href='test_imagen.php'>🔍 Test completo</a></li>";
echo "<li><a href='index.php'>🏠 Página principal</a></li>";
echo "</ul>";
