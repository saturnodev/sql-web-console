#!/bin/bash
set -e

# Función para esperar a que MySQL esté disponible
wait_for_mysql() {
    echo "Esperando a que MySQL esté disponible..."
    # Usar PHP para verificar la conexión
    while ! php -r "
    try {
        \$pdo = new PDO('mysql:host=${DB_HOST:-mysql};port=${DB_PORT:-3306}', '${DB_USER:-root}', '${DB_PASSWORD:-password}');
        echo 'MySQL está listo!\n';
        exit(0);
    } catch (Exception \$e) {
        exit(1);
    }
    " 2>/dev/null; do
        sleep 2
    done
}

# Función para configurar la aplicación
setup_application() {
    echo "Configurando la aplicación..."
    
    # Crear archivo de configuración si no existe
    if [ ! -f /var/www/html/config.php ]; then
        echo "Creando archivo de configuración..."
        cat > /var/www/html/config.php << EOF
<?php
// Configuración de la aplicación
define('APP_SECRET_KEY', '${APP_SECRET_KEY:-$(openssl rand -hex 32)}');
define('APP_ENV', 'production');

// Configuración de la base de datos MySQL
define('DB_HOST', '${DB_HOST:-mysql}');
define('DB_PORT', '${DB_PORT:-3306}');
define('DB_USER', '${DB_USER:-root}');
define('DB_PASSWORD', '${DB_PASSWORD:-password}');
define('DB_NAME', '${DB_NAME:-sql_console}');

// Configuración de seguridad
define('JWT_SECRET_KEY', '${JWT_SECRET_KEY:-$(openssl rand -hex 32)}');
define('TOKEN_EXPIRY_HOURS', ${TOKEN_EXPIRY_HOURS:-24});

// Configuración global para las clases
\$config = [
    'APP_SECRET_KEY' => APP_SECRET_KEY,
    'APP_ENV' => APP_ENV,
    'DB_HOST' => DB_HOST,
    'DB_PORT' => DB_PORT,
    'DB_USER' => DB_USER,
    'DB_PASSWORD' => DB_PASSWORD,
    'DB_NAME' => DB_NAME,
    'JWT_SECRET_KEY' => JWT_SECRET_KEY,
    'TOKEN_EXPIRY_HOURS' => TOKEN_EXPIRY_HOURS
];
?>
EOF
    fi
    
    # Crear directorio data si no existe
    mkdir -p /var/www/html/data
    
    # Crear archivo de tokens si no existe
    if [ ! -f /var/www/html/data/tokens.json ]; then
        echo '[]' > /var/www/html/data/tokens.json
    fi
    
    # Configurar permisos (ignorando errores)
    chown -R www-data:www-data /var/www/html 2>/dev/null || true
    chmod -R 755 /var/www/html 2>/dev/null || true
    chmod 600 /var/www/html/data/tokens.json 2>/dev/null || true
    
    echo "Aplicación configurada correctamente"
}

# Función para crear base de datos si no existe
create_database() {
    echo "Verificando base de datos..."
    
    # Crear base de datos si no existe usando PHP
    php -r "
    try {
        \$pdo = new PDO('mysql:host=${DB_HOST:-mysql};port=${DB_PORT:-3306}', '${DB_USER:-root}', '${DB_PASSWORD:-password}');
        \$pdo->exec('CREATE DATABASE IF NOT EXISTS \`${DB_NAME:-sql_console}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        echo 'Base de datos verificada\n';
    } catch (Exception \$e) {
        echo 'Error verificando base de datos: ' . \$e->getMessage() . '\n';
    }
    "
}

# Función para crear usuario administrador inicial
create_admin_user() {
    echo "Creando usuario administrador inicial..."
    
    # Crear token para usuario admin si no existe
    if [ ! -f /var/www/html/data/admin_created ]; then
        # Generar un token simple para el usuario admin
        ADMIN_TOKEN=$(openssl rand -hex 32)
        
        # Crear el archivo de tokens con el usuario admin
        cat > /var/www/html/data/tokens.json << EOF
[
    {
        "username": "admin",
        "token": "$ADMIN_TOKEN",
        "created": "$(date -u +%Y-%m-%dT%H:%M:%SZ)",
        "expires": "$(date -u -d '+24 hours' +%Y-%m-%dT%H:%M:%SZ)"
    }
]
EOF
        
        echo "Usuario: admin"
        echo "Token: $ADMIN_TOKEN"
        echo "Guarda este token de forma segura!"
        
        touch /var/www/html/data/admin_created
        echo "Usuario administrador creado"
    else
        echo "Usuario administrador ya existe"
    fi
}

# Ejecutar configuración
echo "Iniciando SQL Web Console..."

# Esperar a MySQL si está configurado
if [ -n "$DB_HOST" ]; then
    wait_for_mysql
    create_database
fi

# Configurar aplicación
setup_application

# Crear usuario admin
create_admin_user

echo "SQL Web Console está listo!"
echo "Accede a http://localhost:8080"

# Ejecutar comando original
exec "$@" 