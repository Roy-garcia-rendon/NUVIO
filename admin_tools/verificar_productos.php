<?php
// Script simple para verificar productos en la base de datos
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Verificación de Productos</h1>";

// Conexión directa
$conn = new mysqli("localhost", "root", "", "nuvio");

if ($conn->connect_error) {
    echo "❌ Error de conexión: " . $conn->connect_error;
    exit();
}

echo "✅ Conexión exitosa<br><br>";

// 1. Contar productos
$result = $conn->query("SELECT COUNT(*) as total FROM productos");
$row = $result->fetch_assoc();
$total_productos = $row['total'];

echo "<h2>📊 Total de productos: $total_productos</h2>";

if ($total_productos == 0) {
    echo "⚠️ No hay productos en la base de datos<br>";
    echo "<p><a href='agregar_productos_prueba.php'>Agregar productos de prueba</a></p>";
    exit();
}

// 2. Mostrar todos los productos
echo "<h2>📋 Lista de Productos</h2>";
$sql = "SELECT id, nombre, descripcion, precio, stock, LENGTH(imagen) as tamano_imagen, tipo_imagen FROM productos ORDER BY id";
$result = $conn->query($sql);

if (!$result) {
    echo "❌ Error en consulta: " . $conn->error;
    exit();
}

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #f0f0f0;'>";
echo "<th>ID</th><th>Nombre</th><th>Precio</th><th>Stock</th><th>Tamaño Imagen</th><th>Tipo Imagen</th>";
echo "</tr>";

$contador = 0;
while ($row = $result->fetch_assoc()) {
    $contador++;
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
    echo "<td>$" . number_format($row['precio'], 2) . "</td>";
    echo "<td>" . $row['stock'] . "</td>";
    echo "<td>" . ($row['tamano_imagen'] ? $row['tamano_imagen'] . " bytes" : "NULL") . "</td>";
    echo "<td>" . ($row['tipo_imagen'] ? $row['tipo_imagen'] : "NULL") . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<br><strong>Productos mostrados en la tabla: $contador</strong><br>";

// 3. Simular exactamente lo que hace index.php
echo "<h2>🎯 Simulación de index.php</h2>";
echo "<p>Esta es exactamente la consulta que usa index.php:</p>";

$sql_index = "SELECT id, nombre, descripcion, precio, stock, imagen, tipo_imagen FROM productos";
$resultado_index = mysqli_query($conn, $sql_index);

if (!$resultado_index) {
    echo "❌ Error en consulta de index.php: " . mysqli_error($conn);
} else {
    $num_rows = mysqli_num_rows($resultado_index);
    echo "📊 Productos encontrados por index.php: $num_rows<br><br>";

    echo "<h3>Productos que index.php procesaría:</h3>";
    $contador_index = 0;

    while ($row = mysqli_fetch_assoc($resultado_index)) {
        $contador_index++;
        echo "<div style='border: 1px solid #ddd; margin: 10px 0; padding: 10px; border-radius: 5px;'>";
        echo "<strong>Producto #$contador_index:</strong><br>";
        echo "ID: " . $row['id'] . "<br>";
        echo "Nombre: " . htmlspecialchars($row['nombre']) . "<br>";
        echo "Precio: $" . number_format($row['precio'], 2) . "<br>";
        echo "Stock: " . $row['stock'] . "<br>";
        echo "Tiene imagen: " . (!empty($row['imagen']) ? "SÍ" : "NO") . "<br>";
        echo "URL imagen: <a href='mostrar_imagen.php?id=" . $row['id'] . "' target='_blank'>mostrar_imagen.php?id=" . $row['id'] . "</a><br>";
        echo "</div>";
    }

    echo "<br><strong>Productos procesados por index.php: $contador_index</strong><br>";
}

// 4. Verificar si hay problemas con la consulta
echo "<h2>🔍 Verificación de Consulta</h2>";
$result_test = $conn->query("SELECT * FROM productos LIMIT 5");
if ($result_test) {
    echo "✅ Consulta básica funciona<br>";
    echo "📊 Filas en consulta básica: " . $result_test->num_rows . "<br>";
} else {
    echo "❌ Error en consulta básica: " . $conn->error . "<br>";
}

// 5. Verificar estructura de la tabla
echo "<h2>🏗️ Estructura de la Tabla</h2>";
$result_structure = $conn->query("DESCRIBE productos");
if ($result_structure) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr style='background: #f0f0f0;'><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th></tr>";
    while ($row = $result_structure->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

$conn->close();

echo "<hr>";
echo "<h2>🎯 Diagnóstico</h2>";

if ($total_productos == 0) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 10px;'>";
    echo "<h3>❌ Problema: No hay productos</h3>";
    echo "<p>La base de datos está vacía.</p>";
    echo "<p><a href='agregar_productos_prueba.php'>Agregar productos de prueba</a></p>";
    echo "</div>";
} elseif ($total_productos == 1) {
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 10px;'>";
    echo "<h3>⚠️ Solo hay 1 producto</h3>";
    echo "<p>Solo hay un producto en la base de datos.</p>";
    echo "<p><a href='agregar_productos_prueba.php'>Agregar más productos</a></p>";
    echo "</div>";
} else {
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 10px;'>";
    echo "<h3>✅ Hay $total_productos productos</h3>";
    echo "<p>Si solo ves uno en index.php, puede ser un problema de:</p>";
    echo "<ul>";
    echo "<li>Cache del navegador</li>";
    echo "<li>Problema con mostrar_imagen.php</li>";
    echo "<li>Error en el bucle while de index.php</li>";
    echo "</ul>";
    echo "</div>";
}

echo "<h2>🔗 Enlaces de Prueba</h2>";
echo "<ul>";
echo "<li><a href='index.php'>🏠 Página principal</a></li>";
echo "<li><a href='debug_index.php'>🔍 Debug específico de index.php</a></li>";
echo "<li><a href='mostrar_imagen.php?id=1&debug=1' target='_blank'>🧪 Probar mostrar_imagen.php</a></li>";
echo "</ul>";
