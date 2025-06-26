# Herramientas de Administración - NUVIO

Esta carpeta contiene todas las herramientas de administración, debug, testing y diagnóstico del sistema NUVIO.

## Categorías de Herramientas

### 🔍 Debug y Testing
- **debug_simple.php** - Herramienta de debug básica para verificar el estado del sistema
- **debug_index.php** - Debug específico para la página de inicio
- **index_debug.php** - Versión de debug de la página principal

### 🖼️ Gestión de Imágenes
- **test_imagen.php** - Pruebas de carga y procesamiento de imágenes
- **test_mostrar_imagen.php** - Pruebas para mostrar imágenes desde la base de datos
- **mostrar_imagen.php** - Herramienta para mostrar imágenes almacenadas
- **mover_imagen_defecto.php** - Mover imágenes por defecto a ubicaciones específicas
- **setup_imagenes.php** - Configuración inicial del sistema de imágenes

### ✅ Verificación
- **verificar_productos.php** - Verificar la integridad de los productos en la base de datos
- **verificar_tipo_imagen.php** - Verificar tipos de archivo de imágenes
- **verificar_y_agregar_imagenes.php** - Verificar y agregar imágenes faltantes

### 🔬 Diagnóstico
- **diagnostico_imagenes_detallado.php** - Diagnóstico completo del sistema de imágenes
- **DIAGNOSTICO_IMAGENES.md** - Documentación del diagnóstico de imágenes

### 🧪 Pruebas
- **agregar_productos_prueba.php** - Agregar productos de prueba al sistema

## Acceso

Todas estas herramientas son accesibles desde el Panel de Administración principal en `admin_panel.php`. Los enlaces están organizados por categorías para facilitar su uso.

## Notas Importantes

- Estas herramientas están destinadas únicamente para administradores del sistema
- Algunas herramientas pueden modificar datos en la base de datos
- Se recomienda hacer respaldos antes de usar herramientas de diagnóstico y verificación
- Todas las herramientas requieren autenticación de administrador

## Uso

1. Accede al Panel de Administración
2. Navega a la sección "Herramientas de Administración"
3. Selecciona la categoría correspondiente
4. Haz clic en la herramienta que necesites usar

---

**Desarrollado para NUVIO - Sistema de Gestión de Productos** 