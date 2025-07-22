#!/bin/bash
set -e

# Función para esperar a que MySQL esté disponible
wait_for_mysql() {
    echo "Esperando a que MySQL esté disponible..."
    while ! mysqladmin ping -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASSWORD" --silent; do
        sleep 2
    done
    echo "MySQL está listo!"
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

// Configurar las clases de configuración
App\\Config\\App::setConfig([
    'APP_SECRET_KEY' => APP_SECRET_KEY,
    'APP_ENV' => APP_ENV,
    'JWT_SECRET_KEY' => JWT_SECRET_KEY,
    'TOKEN_EXPIRY_HOURS' => TOKEN_EXPIRY_HOURS
]);

App\\Config\\Database::setConfig([
    'DB_HOST' => DB_HOST,
    'DB_PORT' => DB_PORT,
    'DB_USER' => DB_USER,
    'DB_PASSWORD' => DB_PASSWORD,
    'DB_NAME' => DB_NAME
]);
?>
EOF
    fi
    
    # Crear directorio data si no existe
    mkdir -p /var/www/html/data
    
    # Crear archivo de tokens si no existe
    if [ ! -f /var/www/html/data/tokens.json ]; then
        echo '[]' > /var/www/html/data/tokens.json
    fi
    
    # Configurar permisos
    chown -R www-data:www-data /var/www/html
    chmod -R 755 /var/www/html
    chmod 600 /var/www/html/data/tokens.json
    
    echo "Aplicación configurada correctamente"
}

# Función para crear base de datos si no existe
create_database() {
    echo "Verificando base de datos..."
    
    # Crear base de datos si no existe
    mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null || true
    
    echo "Base de datos verificada"
}

# Función para crear usuario administrador inicial
create_admin_user() {
    echo "Creando usuario administrador inicial..."
    
    # Crear token para usuario admin si no existe
    if [ ! -f /var/www/html/data/admin_created ]; then
        # Usar PHP para crear el token
        php -r "
        require_once '/var/www/html/config.php';
        require_once '/var/www/html/app/Models/AuthModel.php';
        
        \$auth = new App\\Models\\AuthModel();
        \$token = \$auth->createToken('admin');
        
        echo \"Usuario: admin\nToken: \$token\n\";
        echo \"Guarda este token de forma segura!\n\";
        "
        
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