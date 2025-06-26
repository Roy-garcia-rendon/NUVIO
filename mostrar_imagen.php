<?php
// Archivo para mostrar imágenes BLOB desde la base de datos
session_start();

// Habilitar debugging si se solicita
$debug = isset($_GET['debug']) && $_GET['debug'] == '1';

if ($debug) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    echo "<h2>🔍 Debug: mostrar_imagen.php</h2>";
}

// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nuvio";

if ($debug) echo "📊 Conectando a BD: $dbname<br>";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    if ($debug) {
        echo "❌ Error de conexión: " . $conn->connect_error;
        exit();
    } else {
        // Si hay error de conexión, mostrar imagen por defecto
        header('Content-Type: image/jpeg');
        header('Cache-Control: public, max-age=86400');
        $default_dir = 'C:/ProgramData/MySQL/MySQL Server 9.0/Uploads';
        $default_files = glob($default_dir . '/*.jpg');
        if (!empty($default_files)) {
            readfile($default_files[0]);
        }
        exit();
    }
}

if ($debug) echo "✅ Conexión exitosa<br>";

// Verificar que se proporcionó un ID de producto
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    if ($debug) {
        echo "❌ ID de producto inválido o faltante<br>";
        echo "ID recibido: " . (isset($_GET['id']) ? $_GET['id'] : 'NO DEFINIDO') . "<br>";
        exit();
    } else {
        // Imagen por defecto si no hay ID válido
        header('Content-Type: image/jpeg');
        header('Cache-Control: public, max-age=86400');
        $default_dir = 'C:/ProgramData/MySQL/MySQL Server 9.0/Uploads';
        $default_files = glob($default_dir . '/*.jpg');
        if (!empty($default_files)) {
            readfile($default_files[0]);
        }
        exit();
    }
}

$id_producto = (int)$_GET['id'];

if ($debug) echo "🔍 Buscando producto ID: $id_producto<br>";

// Verificar si las columnas existen
$check_columns = $conn->query("SHOW COLUMNS FROM productos LIKE 'imagen'");
if ($check_columns->num_rows == 0) {
    if ($debug) {
        echo "❌ La columna 'imagen' no existe en la tabla productos<br>";
        echo "💡 Ejecuta: ALTER TABLE productos ADD COLUMN imagen LONGBLOB, ADD COLUMN tipo_imagen VARCHAR(100);<br>";
        exit();
    } else {
        header('Content-Type: image/jpeg');
        header('Cache-Control: public, max-age=86400');
        $default_dir = 'C:/ProgramData/MySQL/MySQL Server 9.0/Uploads';
        $default_files = glob($default_dir . '/*.jpg');
        if (!empty($default_files)) {
            readfile($default_files[0]);
        }
        exit();
    }
}

// Consulta SQL
$sql = "SELECT imagen, tipo_imagen FROM productos WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($fila = $resultado->fetch_assoc()) {
        if ($debug) {
            echo "✅ Producto encontrado<br>";
            echo "📦 Tiene imagen: " . ($fila['imagen'] ? 'SÍ' : 'NO') . "<br>";
            echo "🏷️ Tipo de imagen: " . ($fila['tipo_imagen'] ? $fila['tipo_imagen'] : 'NULL') . "<br>";
            echo "📏 Tamaño de imagen: " . ($fila['imagen'] ? strlen($fila['imagen']) . ' bytes' : '0 bytes') . "<br>";
        }

        if ($fila['imagen'] && $fila['tipo_imagen']) {
            if ($debug) {
                echo "🎯 Mostrando imagen desde BD<br>";
                echo "Content-Type: " . $fila['tipo_imagen'] . "<br>";
            } else {
                // Configurar headers para cache
                header('Content-Type: ' . $fila['tipo_imagen']);
                header('Cache-Control: public, max-age=86400');
                header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));

                // Mostrar la imagen desde la base de datos
                echo $fila['imagen'];
            }
        } else {
            if ($debug) {
                echo "⚠️ No hay imagen en BD, mostrando imagen por defecto<br>";
            } else {
                // Mostrar imagen por defecto si no hay imagen en la BD
                header('Content-Type: image/jpeg');
                header('Cache-Control: public, max-age=86400');
                $default_dir = 'C:/ProgramData/MySQL/MySQL Server 9.0/Uploads';
                $default_files = glob($default_dir . '/*.jpg');
                if (!empty($default_files)) {
                    readfile($default_files[0]);
                }
            }
        }
    } else {
        if ($debug) {
            echo "❌ Producto no encontrado<br>";
        } else {
            // Mostrar imagen por defecto si no se encuentra el producto
            header('Content-Type: image/jpeg');
            header('Cache-Control: public, max-age=86400');
            $default_dir = 'C:/ProgramData/MySQL/MySQL Server 9.0/Uploads';
            $default_files = glob($default_dir . '/*.jpg');
            if (!empty($default_files)) {
                readfile($default_files[0]);
            }
        }
    }

    $stmt->close();
} else {
    if ($debug) {
        echo "❌ Error en la preparación de la consulta: " . $conn->error . "<br>";
    } else {
        // Error en la preparación de la consulta
        header('Content-Type: image/jpeg');
        header('Cache-Control: public, max-age=86400');
        $default_dir = 'C:/ProgramData/MySQL/MySQL Server 9.0/Uploads';
        $default_files = glob($default_dir . '/*.jpg');
        if (!empty($default_files)) {
            readfile($default_files[0]);
        }
    }
}

$conn->close();

if ($debug) {
    echo "<hr>";
    echo "<h3>🔗 Prueba sin debug:</h3>";
    echo "<a href='mostrar_imagen.php?id=$id_producto'>mostrar_imagen.php?id=$id_producto</a>";
}
