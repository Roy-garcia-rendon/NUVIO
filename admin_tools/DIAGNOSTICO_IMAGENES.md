# 🔍 Diagnóstico de Imágenes - NUVIO

## Problema
Las imágenes de productos no se muestran en la aplicación NUVIO.

## Análisis del Sistema

### Arquitectura de Imágenes
- **Almacenamiento**: Imágenes BLOB en base de datos MySQL
- **Servidor**: `mostrar_imagen.php` - Script que sirve imágenes desde la BD
- **Visualización**: `index.php` - Muestra productos con imágenes
- **Fallback**: `C:/ProgramData/MySQL/MySQL Server 9.0/Uploads/` - Carpeta de imágenes por defecto

### Archivos Clave
1. `mostrar_imagen.php` - Servidor de imágenes BLOB
2. `index.php` - Página principal con productos
3. `setup_imagenes.php` - Configuración de BD para imágenes
4. `test_imagen.php` - Test completo del sistema
5. `debug_simple.php` - Debug básico
6. `agregar_productos_prueba.php` - Agregar productos de prueba

## Pasos de Diagnóstico

### 1. Verificación Inicial
```bash
# Ejecutar debug simple
http://localhost/Nuvio/debug_simple.php
```

**Verificar:**
- ✅ Conexión a base de datos
- ✅ Tabla `productos` existe
- ✅ Columnas `imagen` y `tipo_imagen` existen
- ✅ Productos en la base de datos
- ✅ Archivos de imagen en `C:/ProgramData/MySQL/MySQL Server 9.0/Uploads/`

### 2. Configuración de Base de Datos
Si faltan columnas, ejecutar:
```bash
http://localhost/Nuvio/setup_imagenes.php
```

**Este script:**
- Agrega columnas `imagen` (LONGBLOB) y `tipo_imagen` (VARCHAR(100))
- Crea carpeta `C:/ProgramData/MySQL/MySQL Server 9.0/Uploads/` si no existe
- Genera imágenes por defecto en la carpeta Uploads

### 3. Agregar Productos de Prueba
Si no hay productos con imágenes:
```bash
http://localhost/Nuvio/agregar_productos_prueba.php
```

**Este script:**
- Crea 5 productos de prueba
- Genera imágenes simples con GD
- Almacena imágenes como BLOB en la BD

### 4. Test Completo
```bash
http://localhost/Nuvio/test_imagen.php
```

**Verificar:**
- Estructura completa de la BD
- Datos de productos
- Funcionamiento de `mostrar_imagen.php`
- Enlaces de prueba

## Posibles Problemas y Soluciones

### ❌ Problema: Columnas faltantes
**Síntomas:** Error "La columna 'imagen' no existe"
**Solución:** Ejecutar `setup_imagenes.php`

### ❌ Problema: No hay productos
**Síntomas:** Página vacía o sin imágenes
**Solución:** Ejecutar `agregar_productos_prueba.php`

### ❌ Problema: Imágenes no se cargan
**Síntomas:** Imágenes rotas o no aparecen
**Solución:** 
1. Verificar `mostrar_imagen.php?id=1&debug=1`
2. Revisar headers HTTP
3. Verificar permisos de archivos

### ❌ Problema: Error de conexión
**Síntomas:** "Error de conexión a la base de datos"
**Solución:**
- Verificar XAMPP está corriendo
- Verificar base de datos `nuvio` existe
- Verificar credenciales en `include/conexion.php`

### ❌ Problema: Carpeta de imágenes por defecto no existe
**Síntomas:** Error 404 en imágenes por defecto
**Solución:** `setup_imagenes.php` crea automáticamente la carpeta y las imágenes

## Estructura de Base de Datos Requerida

```sql
-- Tabla productos debe tener estas columnas:
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL,
    imagen LONGBLOB,           -- ← REQUERIDO
    tipo_imagen VARCHAR(100)   -- ← REQUERIDO
);
```

## Flujo de Funcionamiento

1. **index.php** consulta productos con `imagen` y `tipo_imagen`
2. Para cada producto, genera: `<img src="mostrar_imagen.php?id=X">`
3. **mostrar_imagen.php**:
   - Recibe ID del producto
   - Consulta imagen BLOB de la BD
   - Establece headers HTTP correctos
   - Sirve la imagen o imagen por defecto desde la carpeta Uploads

## Comandos de Prueba

### Debug de mostrar_imagen.php
```
http://localhost/Nuvio/mostrar_imagen.php?id=1&debug=1
```

### Test de imagen específica
```
http://localhost/Nuvio/mostrar_imagen.php?id=1
```

### Verificar estructura BD
```sql
DESCRIBE productos;
SELECT id, nombre, LENGTH(imagen) as tamano FROM productos LIMIT 5;
```

## Logs y Debugging

### Habilitar debug en mostrar_imagen.php
Agregar `&debug=1` a la URL para ver información detallada.

### Verificar headers HTTP
```php
// En mostrar_imagen.php
header('Content-Type: ' . $fila['tipo_imagen']);
header('Cache-Control: public, max-age=86400');
```

### Verificar tamaño de imágenes
```sql
SELECT id, nombre, LENGTH(imagen) as bytes 
FROM productos 
WHERE imagen IS NOT NULL;
```

## Checklist de Verificación

- [ ] XAMPP corriendo (Apache + MySQL)
- [ ] Base de datos `nuvio` existe
- [ ] Tabla `productos` existe
- [ ] Columnas `imagen` y `tipo_imagen` existen
- [ ] Hay productos en la base de datos
- [ ] Productos tienen imágenes BLOB
- [ ] Carpeta `C:/ProgramData/MySQL/MySQL Server 9.0/Uploads/` existe
- [ ] Hay archivos de imagen en la carpeta Uploads
- [ ] `mostrar_imagen.php` funciona sin errores
- [ ] Headers HTTP se establecen correctamente
- [ ] Imágenes se muestran en `index.php`

## Enlaces Útiles

- **Debug Simple**: `debug_simple.php`
- **Setup Completo**: `setup_imagenes.php`
- **Test Completo**: `test_imagen.php`
- **Agregar Productos**: `agregar_productos_prueba.php`
- **Página Principal**: `index.php`

## Soporte

Si el problema persiste después de seguir estos pasos:

1. Ejecutar todos los scripts de diagnóstico
2. Revisar logs de error de PHP/Apache
3. Verificar permisos de archivos y carpetas
4. Comprobar configuración de PHP (extensión GD)
5. Verificar configuración de MySQL (max_allowed_packet) 