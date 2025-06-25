# 🛍️ NUVIO - Tienda Online

![NUVIO Logo](https://img.shields.io/badge/NUVIO-Tienda%20Online-667eea?style=for-the-badge&logo=shopping-cart)
![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3.0-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)

## 📋 Descripción del Proyecto

**NUVIO** es una aplicación web completa de comercio electrónico desarrollada en PHP que permite a los usuarios registrarse, iniciar sesión, explorar productos, gestionar un carrito de compras y administrar productos (para administradores). La aplicación cuenta con un diseño moderno y responsivo que ofrece una experiencia de usuario excepcional.

### 🎯 Características Principales

#### 👤 **Sistema de Usuarios**
- ✅ Registro de usuarios con validación en tiempo real
- 🔐 Inicio de sesión seguro con hash de contraseñas
- 👤 Gestión de perfiles de usuario
- 🔒 Sistema de roles (Usuario/Administrador)
- 🚪 Cerrar sesión seguro

#### 🛒 **Carrito de Compras**
- 🛍️ Agregar productos al carrito
- 📊 Actualizar cantidades en tiempo real
- 🗑️ Eliminar productos individuales
- 🧹 Vaciar carrito completo
- 💰 Cálculo automático de totales
- 💾 Persistencia de carrito en base de datos

#### 📦 **Gestión de Productos**
- 📋 Listado de productos con diseño de tarjetas
- 🔍 Vista detallada de productos
- 📊 Información de stock en tiempo real
- 💰 Precios formateados
- 🖼️ Imágenes de productos

#### ⚙️ **Panel de Administración**
- 📊 Dashboard con estadísticas en tiempo real
- ➕ Agregar nuevos productos
- ✏️ Editar productos existentes
- 🗑️ Eliminar productos
- 📈 Gestión de inventario
- 👥 Gestión de usuarios

#### 🎨 **Diseño y UX**
- 🎨 Diseño moderno con glassmorphism
- 📱 Completamente responsivo
- 🎭 Animaciones CSS suaves
- 🌈 Gradientes y efectos visuales
- ⚡ Interacciones JavaScript fluidas
- 🎯 Navegación intuitiva

## 🛠️ Tecnologías Utilizadas

### **Backend**
- **PHP 8.0+** - Lenguaje de programación principal
- **MySQL** - Base de datos relacional
- **Sesiones PHP** - Gestión de estado de usuario
- **Prepared Statements** - Seguridad contra SQL Injection

### **Frontend**
- **HTML5** - Estructura semántica
- **CSS3** - Estilos modernos con animaciones
- **JavaScript ES6+** - Interactividad del lado cliente
- **Bootstrap 5.3.0** - Framework CSS responsivo
- **Font Awesome 6.4.0** - Iconografía
- **Google Fonts (Poppins)** - Tipografía moderna

### **Características de Seguridad**
- 🔐 **Hash de contraseñas** con `password_hash()`
- 🛡️ **Prepared Statements** para prevenir SQL Injection
- 🔒 **Validación de sesiones** en todas las páginas
- 🚫 **Control de acceso** basado en roles
- 🧹 **Escape de datos** con `htmlspecialchars()`

## 📁 Estructura del Proyecto

```
Nuvio/
├── 📁 css/                    # Archivos CSS personalizados
│   ├── login.css
│   ├── registro.css
│   └── style.css
├── 📁 include/                # Archivos de configuración
│   └── conexion.php          # Clase de conexión a BD
├── 📁 media/                  # Archivos multimedia
│   └── camisablanca.jpg      # Imágenes de productos
├── 🔧 admin_panel.php        # Panel de administración
├── 🔄 actualizar_carrito.php # Actualizar cantidades
├── 🛒 carrito.php            # Agregar al carrito
├── 🗑️ eliminar_del_carrito.php # Eliminar productos
├── ✏️ editar_producto.php    # Editar productos
├── 🏠 index.php              # Página principal
├── 🔐 login.php              # Inicio de sesión
├── 🚪 logout.php             # Cerrar sesión
├── 📝 registro.php           # Registro de usuarios
├── 🧹 vaciar_carrito.php     # Vaciar carrito
├── 🛍️ ver_carrito.php       # Ver carrito de compras
└── 📖 README.md              # Este archivo
```

## 🚀 Instalación y Configuración

### **Requisitos Previos**
- 🖥️ **XAMPP** (Apache + MySQL + PHP) o servidor web similar
- 🌐 **PHP 8.0** o superior
- 🗄️ **MySQL 8.0** o superior
- 📱 **Navegador web moderno**

### **Pasos de Instalación**

1. **Clonar o descargar el proyecto**
   ```bash
   git clone [https://github.com/Roy-garcia-rendon/NUVIO.git]
   cd Nuvio
   ```

2. **Configurar el servidor web**
   - Copiar la carpeta `Nuvio` a `htdocs/` (XAMPP)
   - O configurar el directorio en tu servidor web

3. **Configurar la base de datos**
   ```sql
   -- Crear base de datos
   CREATE DATABASE nuvio;
   USE nuvio;
   
   -- Tabla de usuarios
   CREATE TABLE usuarios (
       id INT AUTO_INCREMENT PRIMARY KEY,
       nombre VARCHAR(100) NOT NULL,
       email VARCHAR(100) UNIQUE NOT NULL,
       contrasena VARCHAR(255) NOT NULL,
       tipo ENUM('usuario', 'admin') DEFAULT 'usuario',
       fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );
   
   -- Tabla de productos
   CREATE TABLE productos (
       id INT AUTO_INCREMENT PRIMARY KEY,
       nombre VARCHAR(100) NOT NULL,
       descripcion TEXT,
       precio DECIMAL(10,2) NOT NULL,
       stock INT NOT NULL DEFAULT 0,
       fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );
   
   -- Tabla de carritos
   CREATE TABLE carritos (
       id INT AUTO_INCREMENT PRIMARY KEY,
       usuario_id INT NOT NULL,
       fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
   );
   
   -- Tabla de productos en carrito
   CREATE TABLE carrito_productos (
       id INT AUTO_INCREMENT PRIMARY KEY,
       carrito_id INT NOT NULL,
       producto_id INT NOT NULL,
       cantidad INT NOT NULL DEFAULT 1,
       FOREIGN KEY (carrito_id) REFERENCES carritos(id),
       FOREIGN KEY (producto_id) REFERENCES productos(id)
   );
   ```

4. **Configurar la conexión**
   - Editar `include/conexion.php` con tus credenciales de BD
   ```php
   $conexion = mysqli_connect("localhost", "tu_usuario", "tu_password", "nuvio");
   ```

5. **Crear usuario administrador**
   ```sql
   INSERT INTO usuarios (nombre, email, contrasena, tipo) 
   VALUES ('Admin', 'admin@nuvio.com', '$2y$10$...', 'admin');
   ```

6. **Acceder a la aplicación**
   - Abrir navegador: `http://localhost/Nuvio/`
   - Iniciar sesión con las credenciales de administrador

## 🎮 Cómo Usar la Aplicación

### **Para Usuarios Regulares**

1. **Registro/Login**
   - Crear cuenta nueva en "Regístrate"
   - O iniciar sesión con cuenta existente

2. **Explorar Productos**
   - Ver productos en la página principal
   - Información detallada de cada producto

3. **Gestionar Carrito**
   - Agregar productos al carrito
   - Modificar cantidades
   - Eliminar productos
   - Ver total de compra

### **Para Administradores**

1. **Acceso al Panel**
   - Iniciar sesión con cuenta de administrador
   - Ver botón "Panel de administración"

2. **Gestión de Productos**
   - Agregar nuevos productos
   - Editar productos existentes
   - Eliminar productos
   - Ver estadísticas de inventario

3. **Monitoreo**
   - Dashboard con estadísticas en tiempo real
   - Control de stock bajo
   - Gestión de usuarios

## 🎨 Características de Diseño

### **Paleta de Colores**
- **Primario:** `#667eea` (Azul)
- **Secundario:** `#764ba2` (Púrpura)
- **Éxito:** `#28a745` (Verde)
- **Peligro:** `#dc3545` (Rojo)
- **Advertencia:** `#ffc107` (Amarillo)

### **Efectos Visuales**
- 🌟 **Glassmorphism** - Efectos de cristal y transparencia
- 🎭 **Animaciones CSS** - Transiciones suaves
- 🌈 **Gradientes** - Fondos modernos
- 💫 **Formas flotantes** - Elementos animados
- 🎯 **Hover effects** - Interacciones visuales

### **Responsive Design**
- 📱 **Mobile-first** - Optimizado para móviles
- 💻 **Tablet** - Adaptación para tablets
- 🖥️ **Desktop** - Experiencia completa en PC
- 🎨 **Flexible layouts** - Grids adaptativos

## 🔧 Funcionalidades Técnicas

### **Sistema de Sesiones**
- 🔐 Gestión segura de sesiones de usuario
- 🛡️ Protección contra ataques de sesión
- ⏰ Timeout automático de sesiones
- 🔄 Persistencia de carrito entre sesiones

### **Base de Datos**
- 🗄️ **Normalización** - Estructura optimizada
- 🔗 **Relaciones** - Claves foráneas
- 📊 **Índices** - Consultas optimizadas
- 🛡️ **Integridad** - Restricciones de datos

### **Seguridad**
- 🔒 **Autenticación** - Login seguro
- 🛡️ **Autorización** - Control de acceso por roles
- 🧹 **Sanitización** - Limpieza de datos
- 🚫 **CSRF Protection** - Protección contra ataques

## 🚀 Características Avanzadas

### **Validación en Tiempo Real**
- ✅ Verificación de campos mientras escribes
- 🔐 Indicador de fortaleza de contraseña
- 💰 Validación de precios y stock
- 📧 Verificación de formato de email

### **Animaciones y UX**
- 🎬 **Entrada escalonada** - Elementos aparecen secuencialmente
- 🎯 **Intersection Observer** - Animaciones al hacer scroll
- 💫 **Efectos hover** - Interacciones visuales
- 🎨 **Transiciones suaves** - Movimientos fluidos

### **Optimización**
- ⚡ **Carga rápida** - Recursos optimizados
- 📱 **PWA Ready** - Preparado para Progressive Web App
- 🔍 **SEO Friendly** - Estructura semántica
- 🌐 **Cross-browser** - Compatibilidad amplia

## 🤝 Contribución

### **Cómo Contribuir**
1. 🍴 Fork el proyecto
2. 🌿 Crear una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. 💾 Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. 📤 Push a la rama (`git push origin feature/AmazingFeature`)
5. 🔄 Abrir un Pull Request

### **Estándares de Código**
- 📝 **Comentarios** - Código bien documentado
- 🎨 **Formato** - Estilo consistente
- 🧪 **Testing** - Funcionalidad probada
- 📱 **Responsive** - Funciona en todos los dispositivos

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 👨‍💻 Autor

**NUVIO Team**
- 🌐 **Website:** [nuvio.com](https://nuvio.com)
- 📧 **Email:** rodrigoa.gr11@gmail.com
- 🐦 **Instagram:** [@wtffroyz](https://www.instagram.com/wtffroyz/)

## 🙏 Agradecimientos

- 🎨 **Bootstrap** - Framework CSS
- 🔤 **Google Fonts** - Tipografías
- 🎭 **Font Awesome** - Iconos
- 🌐 **Comunidad PHP** - Soporte y recursos

---

<div align="center">

**⭐ Si te gusta este proyecto, ¡dale una estrella! ⭐**


</div> 