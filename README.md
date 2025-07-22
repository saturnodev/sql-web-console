# SQL Web Console - Cyberpunk

Una consola web moderna para bases de datos MySQL con interfaz estilo cyberpunk, autenticación por tokens y arquitectura MVC.

## 🚀 Características

- **Interfaz Cyberpunk**: Diseño moderno con efectos neon y animaciones
- **Autenticación Segura**: Sistema de tokens únicos por usuario
- **Arquitectura MVC**: Código organizado y mantenible
- **Operaciones SQL**: Consulta, actualización y eliminación con confirmaciones
- **Gestión de Tablas**: Explorador visual de estructura y datos
- **Gestión de Tokens**: Crear, revocar y administrar tokens de acceso
- **Responsive**: Compatible con dispositivos móviles y desktop

## 📋 Requisitos

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx)
- Extensiones PHP: PDO, PDO_MySQL, JSON

## 🛠️ Instalación

1. **Clonar el repositorio**
   ```bash
   git clone <url-del-repositorio>
   cd sql-web-console
   ```

2. **Configurar la base de datos**
   - Editar `config.php` con los datos de tu base de datos MySQL
   - O copiar `config.example.php` a `config.php` y modificar

3. **Configurar permisos**
   ```bash
   chmod 755 data/
   chmod 600 data/tokens.json
   ```

4. **Instalar dependencias (opcional)**
   ```bash
   composer install
   ```

5. **Acceder a la aplicación**
   ```
   http://tu-dominio/sql-web-console/
   ```

## ⚙️ Configuración

### Variables de Entorno

Edita el archivo `config.php` con tus configuraciones:

```php
// Base de datos
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_USER', 'tu_usuario');
define('DB_PASSWORD', 'tu_password');
define('DB_NAME', 'tu_base_de_datos');

// Seguridad
define('JWT_SECRET_KEY', 'tu_jwt_secret_key_aqui');
define('TOKEN_EXPIRY_HOURS', 24);
```

### Estructura de Directorios

```
sql-web-console/
├── app/
│   ├── Config/          # Configuraciones
│   ├── Controllers/     # Controladores MVC
│   ├── Models/          # Modelos de datos
│   └── Views/           # Vistas y templates
├── data/                # Datos de la aplicación
├── api.php             # API REST
├── config.php          # Configuración principal
├── index.php           # Punto de entrada
└── README.md           # Documentación
```

## 🔐 Autenticación

### Crear un Token

1. Accede a la sección "Gestión de Tokens"
2. Haz clic en "Crear Token"
3. Ingresa el nombre de usuario
4. Configura la duración (1-168 horas)
5. Guarda el token de forma segura

### Iniciar Sesión

1. Ve a la página de login
2. Ingresa tu nombre de usuario
3. Ingresa tu token
4. Haz clic en "Conectar"

## 💻 Uso

### Consola SQL

- **Ejecutar consultas**: Escribe SQL y presiona "Ejecutar" o Ctrl+Enter
- **Historial**: Las consultas se guardan automáticamente
- **Formateo**: Usa el botón "Formatear" para mejorar la legibilidad
- **Confirmaciones**: Las consultas destructivas requieren confirmación

### Explorador de Tablas

- **Ver tablas**: Lista todas las tablas de la base de datos
- **Estructura**: Muestra campos, tipos y restricciones
- **Datos**: Visualiza los datos con límite configurable

### Gestión de Tokens

- **Crear**: Genera nuevos tokens para usuarios
- **Revocar**: Invalida tokens existentes
- **Configurar**: Ajusta la duración de expiración

## 🔒 Seguridad

### Características de Seguridad

- **Tokens únicos**: Cada usuario tiene su propio token
- **Expiración automática**: Los tokens expiran según configuración
- **Almacenamiento seguro**: Tokens encriptados en archivo protegido
- **Confirmaciones**: Operaciones destructivas requieren confirmación
- **Validación de sesión**: Verificación en cada operación

### Mejores Prácticas

1. **Cambia las claves por defecto** en `config.php`
2. **Usa HTTPS** en producción
3. **Configura permisos** correctos en archivos
4. **Revisa logs** regularmente
5. **Actualiza tokens** periódicamente

## 🎨 Personalización

### Temas

La aplicación incluye un sistema de temas:

- **Cyberpunk**: Tema predeterminado con efectos neon
- **Oscuro**: Tema minimalista oscuro
- **Claro**: Tema claro para entornos de oficina

### CSS Personalizado

Puedes modificar los estilos en `app/Views/layout.php`:

```css
:root {
    --cyberpunk-neon: #00ffff;
    --cyberpunk-pink: #ff0080;
    --cyberpunk-yellow: #ffff00;
    --cyberpunk-purple: #8000ff;
}
```

## 🐛 Solución de Problemas

### Errores Comunes

1. **Error de conexión a MySQL**
   - Verifica credenciales en `config.php`
   - Asegúrate de que MySQL esté ejecutándose

2. **Permisos denegados**
   - Configura permisos correctos en directorio `data/`
   - Verifica permisos del servidor web

3. **Token inválido**
   - Verifica que el token no haya expirado
   - Confirma que el usuario existe

### Logs

Los errores se registran en:
- Logs del servidor web (Apache/Nginx)
- Logs de PHP (error_log)

## 📝 API

### Endpoints Disponibles

- `POST /api.php?action=login` - Autenticación
- `POST /api.php?action=logout` - Cerrar sesión
- `POST /api.php?action=execute_query` - Ejecutar consulta SQL
- `POST /api.php?action=get_tables` - Obtener tablas
- `POST /api.php?action=create_token` - Crear token
- `POST /api.php?action=revoke_token` - Revocar token

### Ejemplo de Uso

```javascript
$.post('api.php', {
    action: 'execute_query',
    sql: 'SELECT * FROM users LIMIT 10'
}, function(response) {
    if (response.success) {
        console.log(response.data);
    }
});
```

## 🤝 Contribuir

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 🆘 Soporte

Si tienes problemas o preguntas:

1. Revisa la documentación
2. Busca en issues existentes
3. Crea un nuevo issue con detalles del problema

## 🔄 Changelog

### v1.0.0
- Lanzamiento inicial
- Interfaz cyberpunk
- Sistema de autenticación por tokens
- Consola SQL completa
- Explorador de tablas
- Gestión de tokens
- Arquitectura MVC

---

**Desarrollado con ❤️ y estilo cyberpunk** 