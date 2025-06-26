<?php
// Debug específico para index.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Debug Específico - index.php</h1>";

// Inicializar conexión a la base de datos
include_once 'include/conexion.php';
$db = new conexion();
$conexion = $db->conex();

echo "<h2>1. Verificación de Conexión</h2>";
if ($conexion) {
    echo "✅ Conexión exitosa a la base de datos<br>";
} else {
    echo "❌ Error de conexión<br>";
    exit();
}

echo "<h2>2. Consulta de Productos</h2>";
$sql = "SELECT id, nombre, descripcion, precio, stock, imagen, tipo_imagen FROM productos";
$resultado = mysqli_query($conexion, $sql);

if (!$resultado) {
    echo "❌ Error en la consulta: " . mysqli_error($conexion) . "<br>";
    exit();
}

$num_productos = mysqli_num_rows($resultado);
echo "📊 Total de productos encontrados: $num_productos<br>";

if ($num_productos == 0) {
    echo "⚠️ No hay productos en la base de datos<br>";
    echo "<p><a href='agregar_productos_prueba.php'>Agregar productos de prueba</a></p>";
    exit();
}

echo "<h2>3. Detalles de Productos</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #f0f0f0;'>";
echo "<th>ID</th><th>Nombre</th><th>Precio</th><th>Stock</th><th>Tiene Imagen</th><th>Tamaño Imagen</th><th>Tipo Imagen</th><th>URL Imagen</th>";
echo "</tr>";

$productos_con_imagen = 0;
$productos_sin_imagen = 0;

while ($row = mysqli_fetch_assoc($resultado)) {
    $tiene_imagen = !empty($row['imagen']);
    $tamano_imagen = $tiene_imagen ? strlen($row['imagen']) : 0;
    $tipo_imagen = $row['tipo_imagen'] ?: 'NULL';
    $url_imagen = "mostrar_imagen.php?id=" . $row['id'];

    if ($tiene_imagen) {
        $productos_con_imagen++;
    } else {
        $productos_sin_imagen++;
    }

    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
    echo "<td>$" . number_format($row['precio'], 2) . "</td>";
    echo "<td>" . $row['stock'] . "</td>";
    echo "<td>" . ($tiene_imagen ? "✅ SÍ" : "❌ NO") . "</td>";
    echo "<td>" . ($tamano_imagen > 0 ? $tamano_imagen . " bytes" : "0 bytes") . "</td>";
    echo "<td>" . $tipo_imagen . "</td>";
    echo "<td><a href='$url_imagen' target='_blank'>Ver imagen</a> | <a href='$url_imagen&debug=1' target='_blank'>Debug</a></td>";
    echo "</tr>";
}
echo "</table>";

echo "<h2>4. Resumen de Imágenes</h2>";
echo "📦 Productos con imagen: $productos_con_imagen<br>";
echo "❌ Productos sin imagen: $productos_sin_imagen<br>";
echo "📊 Total: $num_productos<br>";

echo "<h2>5. Prueba de mostrar_imagen.php</h2>";
// Obtener el primer producto para probar
mysqli_data_seek($resultado, 0);
$primer_producto = mysqli_fetch_assoc($resultado);

if ($primer_producto) {
    $test_id = $primer_producto['id'];
    echo "🧪 Probando con producto ID: $test_id - " . htmlspecialchars($primer_producto['nombre']) . "<br>";

    echo "<h3>Enlaces de prueba:</h3>";
    echo "<ul>";
    echo "<li><a href='mostrar_imagen.php?id=$test_id' target='_blank'>mostrar_imagen.php?id=$test_id</a></li>";
    echo "<li><a href='mostrar_imagen.php?id=$test_id&debug=1' target='_blank'>mostrar_imagen.php?id=$test_id&debug=1</a></li>";
    echo "</ul>";

    // Probar la consulta directamente
    $stmt = $conexion->prepare("SELECT imagen, tipo_imagen FROM productos WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $test_id);
        $stmt->execute();
        $resultado_test = $stmt->get_result();

        if ($fila = $resultado_test->fetch_assoc()) {
            echo "<h3>Resultado de consulta directa:</h3>";
            echo "✅ Producto encontrado<br>";
            echo "📦 Tiene imagen: " . ($fila['imagen'] ? 'SÍ (' . strlen($fila['imagen']) . ' bytes)' : 'NO') . "<br>";
            echo "🏷️ Tipo de imagen: " . ($fila['tipo_imagen'] ? $fila['tipo_imagen'] : 'NULL') . "<br>";
        } else {
            echo "❌ Producto no encontrado en consulta directa<br>";
        }
        $stmt->close();
    }
}

echo "<h2>6. Verificación de Archivos</h2>";
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
    echo "<p><a href='setup_imagenes.php'>Crear imágenes por defecto</a></p>";
}

echo "<h2>7. Simulación de index.php</h2>";
echo "<h3>Productos que se mostrarían:</h3>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;'>";

mysqli_data_seek($resultado, 0);
while ($row = mysqli_fetch_assoc($resultado)) {
    echo "<div style='border: 1px solid #ddd; padding: 15px; border-radius: 10px;'>";
    echo "<h4>" . htmlspecialchars($row['nombre']) . "</h4>";
    echo "<p><strong>ID:</strong> " . $row['id'] . "</p>";
    echo "<p><strong>Precio:</strong> $" . number_format($row['precio'], 2) . "</p>";
    echo "<p><strong>Stock:</strong> " . $row['stock'] . "</p>";
    echo "<p><strong>Imagen:</strong> " . (!empty($row['imagen']) ? "✅ SÍ" : "❌ NO") . "</p>";
    echo "<p><strong>URL:</strong> <a href='mostrar_imagen.php?id=" . $row['id'] . "' target='_blank'>Ver imagen</a></p>";
    echo "</div>";
}
echo "</div>";

mysqli_close($conexion);

echo "<hr>";
echo "<h2>🎯 Diagnóstico y Soluciones</h2>";

if ($productos_sin_imagen > 0) {
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 10px; margin: 10px 0;'>";
    echo "<h3>⚠️ Problema Detectado: Productos sin imágenes</h3>";
    echo "<p>Hay $productos_sin_imagen productos sin imágenes en la base de datos.</p>";
    echo "<p><strong>Solución:</strong> <a href='agregar_productos_prueba.php'>Agregar productos con imágenes</a></p>";
    echo "</div>";
}

if ($num_productos == 0) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 10px; margin: 10px 0;'>";
    echo "<h3>❌ Problema Crítico: No hay productos</h3>";
    echo "<p>No hay productos en la base de datos.</p>";
    echo "<p><strong>Solución:</strong> <a href='agregar_productos_prueba.php'>Agregar productos de prueba</a></p>";
    echo "</div>";
}

if (empty($default_files)) {
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 10px; margin: 10px 0;'>";
    echo "<h3>⚠️ Problema: Imágenes por defecto no existen</h3>";
    echo "<p>No se encontraron imágenes en la carpeta por defecto.</p>";
    echo "<p><strong>Solución:</strong> <a href='setup_imagenes.php'>Crear imágenes por defecto</a></p>";
    echo "</div>";
}

echo "<h2>🔗 Enlaces Útiles</h2>";
echo "<ul>";
echo "<li><a href='index.php'>🏠 Página principal</a></li>";
echo "<li><a href='debug_simple.php'>🔍 Debug simple</a></li>";
echo "<li><a href='test_imagen.php'>🧪 Test completo</a></li>";
echo "<li><a href='setup_imagenes.php'>🔧 Setup de imágenes</a></li>";
echo "</ul>";
