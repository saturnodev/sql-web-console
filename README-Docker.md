# SQL Web Console - Docker

Guía completa para ejecutar SQL Web Console usando Docker.

## 🚀 Inicio Rápido

### Instalación Automática (Recomendado)

```bash
# Clonar el repositorio
git clone <url-del-repositorio>
cd sql-web-console

# Instalación completa automática
make install
```

### Instalación Manual

```bash
# 1. Construir y levantar contenedores
make dev

# 2. Ver logs para obtener el token de admin
make logs
```

## 📋 Requisitos

- Docker 20.10+
- Docker Compose 2.0+
- Make (opcional, pero recomendado)

## 🛠️ Comandos Disponibles

### Comandos Básicos

```bash
# Ver todos los comandos disponibles
make help

# Levantar entorno de desarrollo
make dev

# Detener contenedores
make down

# Ver logs
make logs

# Reiniciar contenedores
make restart
```

### Gestión de Contenedores

```bash
# Construir imágenes
make build

# Ver estado de contenedores
make status

# Abrir shell en el contenedor de la aplicación
make shell

# Conectar a MySQL
make mysql
```

### Backup y Restauración

```bash
# Crear backup
make backup

# Restaurar backup
make restore FILE=backups/backup_20231201_120000.sql
```

### Limpieza

```bash
# Limpiar contenedores y volúmenes
make clean
```

## 🌐 Accesos

Una vez levantada la aplicación:

- **Aplicación Principal**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081
- **MySQL**: localhost:3306

## 🔐 Credenciales por Defecto

### Base de Datos
- **Host**: mysql (dentro de Docker) / localhost (desde host)
- **Puerto**: 3306
- **Usuario**: sqluser
- **Contraseña**: sqlpass
- **Base de datos**: sql_console

### phpMyAdmin
- **Usuario**: root
- **Contraseña**: password

### Aplicación
- **Usuario**: admin
- **Token**: Se genera automáticamente (revisa los logs)

## 🏭 Producción

### Configuración de Producción

1. **Crear archivo .env**:
```bash
make env-example
cp .env.example .env
# Editar .env con tus valores
```

2. **Levantar en producción**:
```bash
make prod
```

### Variables de Entorno para Producción

```bash
# Configuración de MySQL
MYSQL_ROOT_PASSWORD=tu_password_seguro
MYSQL_USER=sqluser
MYSQL_PASSWORD=sqlpass
MYSQL_DATABASE=sql_console

# Configuración de seguridad
APP_SECRET_KEY=tu_app_secret_key_aqui
JWT_SECRET_KEY=tu_jwt_secret_key_aqui
TOKEN_EXPIRY_HOURS=24

# Configuración de puertos
APP_PORT=80
NGINX_PORT=80
NGINX_SSL_PORT=443
```

## 🔒 SSL/HTTPS

### Desarrollo
```bash
# Generar certificados SSL de desarrollo
make ssl
```

### Producción
Para producción, reemplaza los certificados en `docker/nginx/ssl/`:
- `cert.pem` - Certificado SSL
- `key.pem` - Clave privada

## 📊 Estructura de Docker

```
sql-web-console/
├── Dockerfile                 # Imagen de la aplicación
├── docker-compose.yml         # Configuración principal
├── docker-compose.override.yml # Configuración de desarrollo
├── docker-compose.prod.yml    # Configuración de producción
├── docker/
│   ├── mysql/
│   │   └── init.sql          # Script de inicialización
│   ├── nginx/
│   │   ├── nginx.conf        # Configuración de Nginx
│   │   └── ssl/              # Certificados SSL
│   └── scripts/
│       └── generate-ssl.sh   # Script para generar SSL
├── docker-entrypoint.sh      # Script de inicio
├── .dockerignore             # Archivos a ignorar
└── Makefile                  # Comandos útiles
```

## 🔧 Configuración Avanzada

### Personalizar Puertos

Edita `docker-compose.yml` o usa variables de entorno:

```yaml
ports:
  - "8080:80"  # Puerto del host:puerto del contenedor
```

### Agregar Volúmenes

Para persistir datos adicionales:

```yaml
volumes:
  - ./logs:/var/www/html/logs
  - ./uploads:/var/www/html/uploads
```

### Configurar Recursos

```yaml
deploy:
  resources:
    limits:
      memory: 512M
      cpus: '0.5'
    reservations:
      memory: 256M
      cpus: '0.25'
```

## 🐛 Solución de Problemas

### Contenedor no inicia

```bash
# Ver logs detallados
make logs

# Reconstruir imagen
make build

# Limpiar y reinstalar
make clean
make install
```

### Error de conexión a MySQL

```bash
# Verificar estado de MySQL
make status

# Conectar directamente a MySQL
make mysql

# Reiniciar solo MySQL
docker-compose restart mysql
```

### Problemas de permisos

```bash
# Corregir permisos
docker-compose exec app chown -R www-data:www-data /var/www/html
docker-compose exec app chmod -R 755 /var/www/html
docker-compose exec app chmod 600 /var/www/html/data/tokens.json
```

### Limpiar completamente

```bash
# Detener y eliminar todo
make clean

# Eliminar imágenes
docker rmi sql-web-console_app

# Eliminar volúmenes
docker volume prune
```

## 📝 Logs y Monitoreo

### Ver logs en tiempo real
```bash
make logs
```

### Ver logs de un servicio específico
```bash
docker-compose logs -f app
docker-compose logs -f mysql
```

### Ver logs de producción
```bash
docker-compose -f docker-compose.prod.yml logs -f
```

## 🔄 Actualizaciones

### Actualizar la aplicación
```bash
# Detener contenedores
make down

# Obtener cambios
git pull

# Reconstruir y levantar
make build
make up
```

### Actualizar solo la base de datos
```bash
# Backup antes de actualizar
make backup

# Actualizar script de inicialización
# Editar docker/mysql/init.sql

# Recrear contenedor de MySQL
docker-compose down mysql
docker-compose up -d mysql
```

## 🚀 Despliegue en Servidor

### 1. Preparar servidor
```bash
# Instalar Docker
curl -fsSL https://get.docker.com | sh

# Instalar Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/download/v2.20.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose
```

### 2. Clonar y configurar
```bash
git clone <url-del-repositorio>
cd sql-web-console

# Configurar variables de entorno
make env-example
cp .env.example .env
# Editar .env

# Levantar en producción
make prod
```

### 3. Configurar proxy reverso (opcional)
```bash
# Usar Nginx incluido o configurar tu propio proxy
# Ver docker/nginx/nginx.conf para configuración
```

## 📞 Soporte

Si tienes problemas:

1. Revisa los logs: `make logs`
2. Verifica el estado: `make status`
3. Consulta la documentación principal: `README.md`
4. Crea un issue en el repositorio

---

**¡Disfruta usando SQL Web Console con Docker! 🐳** 