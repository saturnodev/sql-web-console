#!/bin/bash

# Script para generar certificados SSL de desarrollo
# Solo usar para desarrollo, no para producción

SSL_DIR="./docker/nginx/ssl"
DOMAIN="localhost"

echo "Generando certificados SSL de desarrollo para $DOMAIN..."

# Crear directorio SSL si no existe
mkdir -p "$SSL_DIR"

# Generar clave privada
openssl genrsa -out "$SSL_DIR/key.pem" 2048

# Generar certificado autofirmado
openssl req -new -x509 -key "$SSL_DIR/key.pem" -out "$SSL_DIR/cert.pem" -days 365 -subj "/C=ES/ST=Madrid/L=Madrid/O=SQL Console/OU=Development/CN=$DOMAIN"

# Configurar permisos
chmod 600 "$SSL_DIR/key.pem"
chmod 644 "$SSL_DIR/cert.pem"

echo "Certificados SSL generados en $SSL_DIR/"
echo "Certificado: $SSL_DIR/cert.pem"
echo "Clave privada: $SSL_DIR/key.pem"
echo ""
echo "⚠️  ADVERTENCIA: Estos son certificados de desarrollo autofirmados."
echo "   Para producción, usa certificados de una autoridad certificadora."
echo ""
echo "Para usar en desarrollo, agrega el certificado a tu navegador o"
echo "acepta la advertencia de seguridad." 