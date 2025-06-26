<?php
// Script para mover la imagen por defecto a la nueva ubicación
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>📁 Configurando Carpeta de Imágenes por Defecto</h2>";

$ruta_original = 'media/camisablanca.jpg';
$ruta_destino = 'C:/ProgramData/MySQL/MySQL Server 9.0/Uploads';

echo "<h3>🔍 Verificando rutas...</h3>";
echo "📂 Ruta original: $ruta_original<br>";
echo "📂 Ruta destino: $ruta_destino<br>";

// Verificar si existe la imagen original
if (file_exists($ruta_original)) {
    echo "✅ Imagen original existe<br>";
    echo "📏 Tamaño: " . filesize($ruta_original) . " bytes<br>";

    // Verificar si la carpeta de destino existe
    if (!is_dir($ruta_destino)) {
        echo "⚠️ El directorio de destino no existe: $ruta_destino<br>";
        echo "🔧 Intentando crear el directorio...<br>";

        if (mkdir($ruta_destino, 0755, true)) {
            echo "✅ Directorio creado exitosamente<br>";
        } else {
            echo "❌ Error al crear el directorio. Verifica permisos.<br>";
            exit();
        }
    } else {
        echo "✅ Directorio de destino existe<br>";
    }

    // Verificar archivos existentes en el directorio destino
    $archivos_existentes = glob($ruta_destino . '/*.jpg');
    if (!empty($archivos_existentes)) {
        echo "⚠️ Ya existen archivos en el directorio destino<br>";
        echo "📏 Archivos encontrados: " . count($archivos_existentes) . "<br>";
        foreach ($archivos_existentes as $archivo) {
            echo "📁 " . basename($archivo) . " (" . filesize($archivo) . " bytes)<br>";
        }

        // Preguntar si sobrescribir
        echo "<h3>🔄 ¿Sobrescribir archivos existentes?</h3>";
        echo "<p>Ya existen archivos en el directorio destino. ¿Deseas sobrescribirlos?</p>";
        echo "<a href='?action=overwrite' class='btn btn-warning'>Sí, sobrescribir</a> ";
        echo "<a href='?action=skip' class='btn btn-secondary'>No, mantener actuales</a>";

        if (isset($_GET['action'])) {
            if ($_GET['action'] === 'overwrite') {
                // Sobrescribir
                $nuevo_archivo = $ruta_destino . '/default.jpg';
                if (copy($ruta_original, $nuevo_archivo)) {
                    echo "<br>✅ Archivo copiado exitosamente como default.jpg<br>";
                } else {
                    echo "<br>❌ Error al copiar el archivo<br>";
                }
            } else {
                echo "<br>⏭️ Manteniendo archivos actuales<br>";
            }
        }
    } else {
        // Copiar la imagen
        echo "<h3>📋 Copiando imagen...</h3>";
        $nuevo_archivo = $ruta_destino . '/default.jpg';
        if (copy($ruta_original, $nuevo_archivo)) {
            echo "✅ Imagen copiada exitosamente como default.jpg<br>";
            echo "📏 Nuevo tamaño: " . filesize($nuevo_archivo) . " bytes<br>";
        } else {
            echo "❌ Error al copiar la imagen<br>";
            echo "💡 Verifica permisos de escritura en el directorio de destino<br>";
        }
    }
} else {
    echo "❌ La imagen original no existe: $ruta_original<br>";
    echo "<h3>🔧 Creando imágenes por defecto en la nueva ubicación...</h3>";

    // Crear imágenes por defecto en la nueva ubicación
    if (!is_dir($ruta_destino)) {
        if (mkdir($ruta_destino, 0755, true)) {
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
        $ruta_completa = $ruta_destino . '/' . $filename;

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
}

// Verificar resultado final
echo "<h3>🎯 Verificación final...</h3>";
$archivos_finales = glob($ruta_destino . '/*.jpg');
if (!empty($archivos_finales)) {
    echo "✅ Imágenes por defecto disponibles en: $ruta_destino<br>";
    echo "📏 Archivos disponibles: " . count($archivos_finales) . "<br>";
    foreach ($archivos_finales as $archivo) {
        echo "📁 " . basename($archivo) . " (" . filesize($archivo) . " bytes)<br>";

        // Verificar permisos
        if (is_readable($archivo)) {
            echo "✅ El archivo es legible<br>";
        } else {
            echo "⚠️ El archivo no es legible (verificar permisos)<br>";
        }
    }
} else {
    echo "❌ No se encontraron imágenes por defecto<br>";
}

echo "<hr>";
echo "<h3>🔗 Enlaces útiles:</h3>";
echo "<ul>";
echo "<li><a href='debug_simple.php'>🔍 Debug simple</a></li>";
echo "<li><a href='test_imagen.php'>🧪 Test completo</a></li>";
echo "<li><a href='index.php'>🏠 Página principal</a></li>";
echo "</ul>";

echo "<h3>📋 Próximos pasos:</h3>";
echo "<ol>";
echo "<li>Verificar que las imágenes se muestran correctamente</li>";
echo "<li>Probar el sistema completo con debug_simple.php</li>";
echo "<li>Verificar que las imágenes funcionan en index.php</li>";
echo "</ol>";
