# Makefile para SQL Web Console
# Comandos útiles para gestionar la aplicación Docker

.PHONY: help build up down restart logs clean install ssl dev prod

# Variables
COMPOSE_DEV = docker-compose -f docker-compose.yml -f docker-compose.override.yml
COMPOSE_PROD = docker-compose -f docker-compose.prod.yml

# Comando por defecto
help:
	@echo "SQL Web Console - Comandos disponibles:"
	@echo ""
	@echo "Desarrollo:"
	@echo "  make dev        - Levantar entorno de desarrollo"
	@echo "  make build      - Construir imágenes Docker"
	@echo "  make up         - Levantar contenedores"
	@echo "  make down       - Detener contenedores"
	@echo "  make restart    - Reiniciar contenedores"
	@echo "  make logs       - Ver logs de los contenedores"
	@echo "  make clean      - Limpiar contenedores y volúmenes"
	@echo ""
	@echo "Producción:"
	@echo "  make prod       - Levantar entorno de producción"
	@echo "  make ssl        - Generar certificados SSL de desarrollo"
	@echo ""
	@echo "Instalación:"
	@echo "  make install    - Instalación completa inicial"
	@echo ""
	@echo "Accesos:"
	@echo "  - Aplicación:   http://localhost:8080"
	@echo "  - phpMyAdmin:   http://localhost:8081"
	@echo "  - MySQL:        localhost:3306"

# Desarrollo
dev: build up
	@echo "🚀 Entorno de desarrollo iniciado"
	@echo "📱 Aplicación: http://localhost:8080"
	@echo "🗄️  phpMyAdmin: http://localhost:8081"
	@echo "🔑 Usuario admin creado automáticamente"

build:
	@echo "🔨 Construyendo imágenes Docker..."
	$(COMPOSE_DEV) build

up:
	@echo "⬆️  Levantando contenedores..."
	$(COMPOSE_DEV) up -d

down:
	@echo "⬇️  Deteniendo contenedores..."
	$(COMPOSE_DEV) down

restart:
	@echo "🔄 Reiniciando contenedores..."
	$(COMPOSE_DEV) restart

logs:
	@echo "📋 Mostrando logs..."
	$(COMPOSE_DEV) logs -f

# Producción
prod: ssl
	@echo "🏭 Configurando entorno de producción..."
	@echo "⚠️  Asegúrate de configurar las variables de entorno:"
	@echo "   - MYSQL_ROOT_PASSWORD"
	@echo "   - MYSQL_USER"
	@echo "   - MYSQL_PASSWORD"
	@echo "   - APP_SECRET_KEY"
	@echo "   - JWT_SECRET_KEY"
	$(COMPOSE_PROD) up -d

# SSL
ssl:
	@echo "🔒 Generando certificados SSL..."
	@chmod +x docker/scripts/generate-ssl.sh
	@./docker/scripts/generate-ssl.sh

# Limpieza
clean:
	@echo "🧹 Limpiando contenedores y volúmenes..."
	$(COMPOSE_DEV) down -v
	$(COMPOSE_PROD) down -v
	docker system prune -f
	@echo "✅ Limpieza completada"

# Instalación completa
install: ssl dev
	@echo ""
	@echo "🎉 Instalación completada!"
	@echo ""
	@echo "📱 Accesos:"
	@echo "   - Aplicación:   http://localhost:8080"
	@echo "   - phpMyAdmin:   http://localhost:8081"
	@echo ""
	@echo "🔑 Credenciales por defecto:"
	@echo "   - Usuario: admin"
	@echo "   - Token: Se genera automáticamente (revisa los logs)"
	@echo ""
	@echo "📋 Próximos pasos:"
	@echo "   1. Accede a http://localhost:8080"
	@echo "   2. Inicia sesión con el usuario 'admin'"
	@echo "   3. El token se muestra en los logs del contenedor"
	@echo "   4. Crea tokens adicionales desde la interfaz"
	@echo ""
	@echo "📝 Para ver los logs: make logs"

# Comandos adicionales
status:
	@echo "📊 Estado de los contenedores:"
	$(COMPOSE_DEV) ps

shell:
	@echo "🐚 Abriendo shell en el contenedor de la aplicación..."
	$(COMPOSE_DEV) exec app bash

mysql:
	@echo "🗄️  Conectando a MySQL..."
	$(COMPOSE_DEV) exec mysql mysql -u root -ppassword sql_console

backup:
	@echo "💾 Creando backup de la base de datos..."
	@mkdir -p backups
	$(COMPOSE_DEV) exec mysql mysqldump -u root -ppassword sql_console > backups/backup_$(shell date +%Y%m%d_%H%M%S).sql
	@echo "✅ Backup creado en backups/"

restore:
	@echo "📥 Restaurando backup..."
	@if [ -z "$(FILE)" ]; then \
		echo "❌ Especifica el archivo de backup: make restore FILE=backups/backup_20231201_120000.sql"; \
		exit 1; \
	fi
	$(COMPOSE_DEV) exec -T mysql mysql -u root -ppassword sql_console < $(FILE)
	@echo "✅ Backup restaurado"

# Variables de entorno
env-example:
	@echo "📝 Creando archivo .env de ejemplo..."
	@echo "# Configuración de MySQL" > .env.example
	@echo "MYSQL_ROOT_PASSWORD=tu_password_seguro" >> .env.example
	@echo "MYSQL_USER=sqluser" >> .env.example
	@echo "MYSQL_PASSWORD=sqlpass" >> .env.example
	@echo "MYSQL_DATABASE=sql_console" >> .env.example
	@echo "" >> .env.example
	@echo "# Configuración de seguridad" >> .env.example
	@echo "APP_SECRET_KEY=tu_app_secret_key_aqui" >> .env.example
	@echo "JWT_SECRET_KEY=tu_jwt_secret_key_aqui" >> .env.example
	@echo "TOKEN_EXPIRY_HOURS=24" >> .env.example
	@echo "" >> .env.example
	@echo "# Configuración de puertos" >> .env.example
	@echo "APP_PORT=80" >> .env.example
	@echo "NGINX_PORT=80" >> .env.example
	@echo "NGINX_SSL_PORT=443" >> .env.example
	@echo "✅ Archivo .env.example creado"
	@echo "📝 Copia .env.example a .env y edita las variables" 