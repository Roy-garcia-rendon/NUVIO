<?php
// Test directo de mostrar_imagen.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🧪 Test Directo de mostrar_imagen.php</h1>";

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nuvio";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo "<p style='color: red;'>❌ Error de conexión: " . $conn->connect_error . "</p>";
    exit();
}

echo "<p style='color: green;'>✅ Conexión exitosa</p>";

// Verificar si hay productos
$result = $conn->query("SELECT id, nombre FROM productos LIMIT 5");
if ($result && $result->num_rows > 0) {
    echo "<h2>📦 Productos disponibles:</h2>";
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>ID: " . $row['id'] . " - " . htmlspecialchars($row['nombre']) . "</li>";
    }
    echo "</ul>";

    // Obtener el primer producto para la prueba
    $first_product_result = $conn->query("SELECT id, nombre FROM productos LIMIT 1");
    $first_product = $first_product_result->fetch_assoc();
    $test_id = $first_product['id'];

    echo "<h2>🔍 Probando mostrar_imagen.php con ID: $test_id</h2>";

    // Simular la llamada a mostrar_imagen.php
    $_GET['id'] = $test_id;
    $_GET['debug'] = '1';

    echo "<h3>📋 Información del producto:</h3>";

    // Verificar si las columnas existen
    $check_columns = $conn->query("SHOW COLUMNS FROM productos LIKE 'imagen'");
    if ($check_columns->num_rows == 0) {
        echo "<p style='color: red;'>❌ La columna 'imagen' no existe</p>";
        echo "<p>💡 Ejecuta: ALTER TABLE productos ADD COLUMN imagen LONGBLOB, ADD COLUMN tipo_imagen VARCHAR(100);</p>";
    } else {
        echo "<p style='color: green;'>✅ La columna 'imagen' existe</p>";

        // Consultar el producto
        $sql = "SELECT imagen, tipo_imagen FROM productos WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("i", $test_id);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($fila = $resultado->fetch_assoc()) {
                echo "<p>✅ Producto encontrado</p>";
                echo "<p>📦 Tiene imagen: " . ($fila['imagen'] ? 'SÍ' : 'NO') . "</p>";
                echo "<p>🏷️ Tipo de imagen: " . ($fila['tipo_imagen'] ? $fila['tipo_imagen'] : 'NULL') . "</p>";
                echo "<p>📏 Tamaño de imagen: " . ($fila['imagen'] ? strlen($fila['imagen']) . ' bytes' : '0 bytes') . "</p>";

                if ($fila['imagen'] && $fila['tipo_imagen']) {
                    echo "<p style='color: green;'>✅ El producto tiene imagen BLOB</p>";
                    echo "<h3>🖼️ Prueba de visualización:</h3>";
                    echo "<img src='mostrar_imagen.php?id=$test_id' style='border: 2px solid #ccc; max-width: 300px;' alt='Test Image'>";
                } else {
                    echo "<p style='color: orange;'>⚠️ El producto no tiene imagen BLOB</p>";
                    echo "<p>💡 Ejecuta <a href='verificar_y_agregar_imagenes.php'>verificar_y_agregar_imagenes.php</a> para agregar imágenes</p>";
                }
            } else {
                echo "<p style='color: red;'>❌ Producto no encontrado</p>";
            }

            $stmt->close();
        } else {
            echo "<p style='color: red;'>❌ Error en la preparación de la consulta: " . $conn->error . "</p>";
        }
    }
} else {
    echo "<p style='color: red;'>❌ No se encontraron productos</p>";
    echo "<p>💡 Ejecuta <a href='verificar_y_agregar_imagenes.php'>verificar_y_agregar_imagenes.php</a> para crear productos</p>";
}

$conn->close();

echo "<hr>";
echo "<h2>🔗 Enlaces de prueba:</h2>";
echo "<ul>";
echo "<li><a href='mostrar_imagen.php?id=1&debug=1' target='_blank'>🔍 Debug con ID 1</a></li>";
echo "<li><a href='mostrar_imagen.php?id=1' target='_blank'>🖼️ Ver imagen ID 1</a></li>";
echo "<li><a href='verificar_y_agregar_imagenes.php'>🖼️ Verificar y agregar imágenes</a></li>";
echo "<li><a href='diagnostico_imagenes_detallado.php'>🔍 Diagnóstico detallado</a></li>";
echo "<li><a href='index.php'>🏠 Página principal</a></li>";
echo "</ul>";
