<?php
// Verificar el campo tipo_imagen en la base de datos
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Verificación del Campo tipo_imagen</h1>";

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

// 1. Verificar si existe la columna tipo_imagen
echo "<h2>1. 📋 Verificando columna tipo_imagen</h2>";

$check_tipo = $conn->query("SHOW COLUMNS FROM productos LIKE 'tipo_imagen'");
if ($check_tipo->num_rows > 0) {
    $columna_info = $check_tipo->fetch_assoc();
    echo "<p style='color: green;'>✅ La columna 'tipo_imagen' existe</p>";
    echo "<p><strong>Tipo:</strong> " . $columna_info['Type'] . "</p>";
    echo "<p><strong>Nulo:</strong> " . $columna_info['Null'] . "</p>";
    echo "<p><strong>Por defecto:</strong> " . $columna_info['Default'] . "</p>";
} else {
    echo "<p style='color: red;'>❌ La columna 'tipo_imagen' NO existe</p>";
    echo "<p>💡 Necesitas crear la columna:</p>";
    echo "<code>ALTER TABLE productos ADD COLUMN tipo_imagen VARCHAR(100);</code>";
}

// 2. Verificar valores actuales en tipo_imagen
echo "<h2>2. 📊 Valores actuales en tipo_imagen</h2>";

$result = $conn->query("SELECT id, nombre, tipo_imagen, LENGTH(imagen) as tamano_imagen FROM productos ORDER BY id");
if ($result && $result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>tipo_imagen</th><th>Tamaño Imagen</th><th>Estado</th></tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
        echo "<td>" . ($row['tipo_imagen'] ? $row['tipo_imagen'] : '<em>NULL</em>') . "</td>";
        echo "<td>" . ($row['tamano_imagen'] ? $row['tamano_imagen'] . ' bytes' : '<em>NULL</em>') . "</td>";

        if ($row['tipo_imagen'] && $row['tamano_imagen']) {
            echo "<td style='color: green;'>✅ Completo</td>";
        } elseif ($row['tamano_imagen'] && !$row['tipo_imagen']) {
            echo "<td style='color: orange;'>⚠️ Falta tipo_imagen</td>";
        } elseif (!$row['tamano_imagen'] && $row['tipo_imagen']) {
            echo "<td style='color: orange;'>⚠️ Falta imagen</td>";
        } else {
            echo "<td style='color: red;'>❌ Sin datos</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ No se encontraron productos</p>";
}

// 3. Valores correctos para tipo_imagen
echo "<h2>3. ✅ Valores correctos para tipo_imagen</h2>";
echo "<p>El campo <code>tipo_imagen</code> debe contener uno de estos valores:</p>";
echo "<ul>";
echo "<li><strong>image/jpeg</strong> - Para archivos .jpg, .jpeg</li>";
echo "<li><strong>image/png</strong> - Para archivos .png</li>";
echo "<li><strong>image/gif</strong> - Para archivos .gif</li>";
echo "<li><strong>image/webp</strong> - Para archivos .webp</li>";
echo "</ul>";

echo "<h3>Ejemplo de uso en mostrar_imagen.php:</h3>";
echo "<pre>";
echo "// Cuando se sirve la imagen:\n";
echo "header('Content-Type: ' . \$fila['tipo_imagen']);\n";
echo "echo \$fila['imagen']; // Datos BLOB de la imagen\n";
echo "</pre>";

// 4. Corregir valores incorrectos
echo "<h2>4. 🔧 Corregir valores incorrectos</h2>";

$incorrectos = $conn->query("SELECT COUNT(*) as total FROM productos WHERE tamano_imagen > 0 AND (tipo_imagen IS NULL OR tipo_imagen = '')");
$count_incorrectos = $incorrectos->fetch_assoc()['total'];

if ($count_incorrectos > 0) {
    echo "<p style='color: orange;'>⚠️ Se encontraron $count_incorrectos productos con imagen pero sin tipo_imagen</p>";
    echo "<form method='post'>";
    echo "<input type='submit' name='corregir_tipos' value='Corregir tipo_imagen (establecer como image/jpeg)' style='background: #ffc107; padding: 10px; border: none; border-radius: 5px; cursor: pointer;'>";
    echo "</form>";
} else {
    echo "<p style='color: green;'>✅ Todos los productos tienen tipo_imagen correcto</p>";
}

// Procesar corrección
if (isset($_POST['corregir_tipos'])) {
    echo "<h3>🔧 Corrigiendo tipo_imagen...</h3>";

    $update_sql = "UPDATE productos SET tipo_imagen = 'image/jpeg' WHERE LENGTH(imagen) > 0 AND (tipo_imagen IS NULL OR tipo_imagen = '')";
    if ($conn->query($update_sql)) {
        $affected = $conn->affected_rows;
        echo "<p style='color: green;'>✅ Se corrigieron $affected productos</p>";
    } else {
        echo "<p style='color: red;'>❌ Error al corregir: " . $conn->error . "</p>";
    }
}

// 5. Crear columna si no existe
if ($check_tipo->num_rows == 0) {
    echo "<h2>5. 🔧 Crear columna tipo_imagen</h2>";
    echo "<form method='post'>";
    echo "<input type='submit' name='crear_columna' value='Crear columna tipo_imagen' style='background: #28a745; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer;'>";
    echo "</form>";
}

if (isset($_POST['crear_columna'])) {
    echo "<h3>🔧 Creando columna tipo_imagen...</h3>";

    $create_sql = "ALTER TABLE productos ADD COLUMN tipo_imagen VARCHAR(100)";
    if ($conn->query($create_sql)) {
        echo "<p style='color: green;'>✅ Columna tipo_imagen creada exitosamente</p>";
    } else {
        echo "<p style='color: red;'>❌ Error al crear columna: " . $conn->error . "</p>";
    }
}

$conn->close();

echo "<hr>";
echo "<h2>🔗 Enlaces útiles:</h2>";
echo "<ul>";
echo "<li><a href='verificar_y_agregar_imagenes.php'>🖼️ Verificar y agregar imágenes</a></li>";
echo "<li><a href='diagnostico_imagenes_detallado.php'>🔍 Diagnóstico detallado</a></li>";
echo "<li><a href='index.php'>🏠 Página principal</a></li>";
echo "</ul>";
