<?php
// Archivo de prueba para diagnosticar problemas con imágenes BLOB
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 Diagnóstico de Imágenes BLOB</h2>";

// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nuvio";

echo "<h3>1. Verificando conexión a la base de datos...</h3>";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    echo "❌ Error de conexión: " . $conn->connect_error;
    exit();
} else {
    echo "✅ Conexión exitosa a la base de datos: $dbname<br>";
}

echo "<h3>2. Verificando estructura de la tabla productos...</h3>";

// Verificar si la tabla existe
$result = $conn->query("SHOW TABLES LIKE 'productos'");
if ($result->num_rows > 0) {
    echo "✅ Tabla 'productos' existe<br>";
} else {
    echo "❌ Tabla 'productos' NO existe<br>";
    exit();
}

// Verificar columnas de la tabla
$result = $conn->query("DESCRIBE productos");
echo "<h4>Columnas de la tabla productos:</h4>";
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Por defecto</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . $row['Key'] . "</td>";
    echo "<td>" . $row['Default'] . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h3>3. Verificando datos en la tabla productos...</h3>";

// Contar productos
$result = $conn->query("SELECT COUNT(*) as total FROM productos");
$row = $result->fetch_assoc();
echo "📊 Total de productos: " . $row['total'] . "<br>";

// Verificar productos con imágenes
$result = $conn->query("SELECT COUNT(*) as con_imagen FROM productos WHERE imagen IS NOT NULL AND imagen != ''");
$row = $result->fetch_assoc();
echo "🖼️ Productos con imagen: " . $row['con_imagen'] . "<br>";

// Mostrar detalles de los primeros 5 productos
$result = $conn->query("SELECT id, nombre, LENGTH(imagen) as tamano_imagen, tipo_imagen FROM productos LIMIT 5");
echo "<h4>Detalles de los primeros 5 productos:</h4>";
echo "<table border='1' style='border-collapse: collapse;'>";
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

echo "<h3>4. Probando mostrar_imagen.php...</h3>";

// Probar con el primer producto
$result = $conn->query("SELECT id FROM productos LIMIT 1");
if ($row = $result->fetch_assoc()) {
    $test_id = $row['id'];
    echo "🧪 Probando con producto ID: $test_id<br>";
    echo "🔗 URL de prueba: <a href='mostrar_imagen.php?id=$test_id' target='_blank'>mostrar_imagen.php?id=$test_id</a><br>";

    // Probar la consulta directamente
    $stmt = $conn->prepare("SELECT imagen, tipo_imagen FROM productos WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $test_id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($fila = $resultado->fetch_assoc()) {
            echo "✅ Producto encontrado<br>";
            echo "📦 Tiene imagen: " . ($fila['imagen'] ? 'SÍ' : 'NO') . "<br>";
            echo "🏷️ Tipo de imagen: " . ($fila['tipo_imagen'] ? $fila['tipo_imagen'] : 'NULL') . "<br>";
            echo "📏 Tamaño de imagen: " . ($fila['imagen'] ? strlen($fila['imagen']) . ' bytes' : '0 bytes') . "<br>";
        } else {
            echo "❌ Producto no encontrado<br>";
        }
        $stmt->close();
    } else {
        echo "❌ Error en la preparación de la consulta<br>";
    }
} else {
    echo "❌ No hay productos en la base de datos<br>";
}

echo "<h3>5. Verificando archivo de imagen por defecto...</h3>";

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

echo "<h3>6. Información del servidor...</h3>";
echo "🌐 Servidor: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "🐘 Versión PHP: " . phpversion() . "<br>";
echo "📁 Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "🔗 Script Name: " . $_SERVER['SCRIPT_NAME'] . "<br>";

$conn->close();

echo "<hr>";
echo "<h3>🎯 Recomendaciones:</h3>";
echo "<ul>";
echo "<li>Si no hay columnas 'imagen' o 'tipo_imagen', ejecuta: ALTER TABLE productos ADD COLUMN imagen LONGBLOB, ADD COLUMN tipo_imagen VARCHAR(100);</li>";
echo "<li>Si no hay productos con imágenes, sube algunas imágenes a la base de datos</li>";
echo "<li>Verifica que hay archivos de imagen en $default_dir</li>";
echo "<li>Revisa los permisos de archivos y carpetas</li>";
echo "</ul>";
