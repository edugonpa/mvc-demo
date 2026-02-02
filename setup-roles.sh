#!/bin/bash

# Script de instalación del sistema de roles
# Laravel 12 - Sistema de Autenticación y Autorización

echo "🚀 Instalando sistema de roles..."
echo ""

# Colores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Función para imprimir con color
print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

# Verificar que estamos en un proyecto Laravel
if [ ! -f "artisan" ]; then
    print_error "Este script debe ejecutarse desde la raíz del proyecto Laravel"
    exit 1
fi

print_success "Proyecto Laravel detectado"
echo ""

# Paso 1: Ejecutar migraciones
echo "📦 Paso 1: Ejecutando migraciones..."
php artisan migrate

if [ $? -eq 0 ]; then
    print_success "Migraciones ejecutadas correctamente"
else
    print_error "Error al ejecutar migraciones"
    exit 1
fi
echo ""

# Paso 2: Ejecutar seeders
echo "🌱 Paso 2: Ejecutando seeders..."
php artisan db:seed --class=UserSeeder

if [ $? -eq 0 ]; then
    print_success "Seeders ejecutados correctamente"
else
    print_error "Error al ejecutar seeders"
    exit 1
fi
echo ""

# Paso 3: Limpiar caché
echo "🧹 Paso 3: Limpiando caché..."
php artisan cache:clear > /dev/null 2>&1
php artisan config:clear > /dev/null 2>&1
php artisan route:clear > /dev/null 2>&1
php artisan view:clear > /dev/null 2>&1
print_success "Caché limpiado"
echo ""

# Paso 4: Mostrar información de usuarios
echo "👥 Usuarios de prueba creados:"
echo ""
echo "┌─────────────────────────────────────────────────────────┐"
echo "│  ADMINISTRADOR                                          │"
echo "├─────────────────────────────────────────────────────────┤"
echo "│  Email:      admin@example.com                          │"
echo "│  Contraseña: password                                   │"
echo "│  Rol:        admin                                      │"
echo "│  Permisos:   Crear, Editar, Eliminar productos          │"
echo "└─────────────────────────────────────────────────────────┘"
echo ""
echo "┌─────────────────────────────────────────────────────────┐"
echo "│  USUARIO REGULAR                                        │"
echo "├─────────────────────────────────────────────────────────┤"
echo "│  Email:      user@example.com                           │"
echo "│  Contraseña: password                                   │"
echo "│  Rol:        user                                       │"
echo "│  Permisos:   Crear, Editar productos (NO eliminar)      │"
echo "└─────────────────────────────────────────────────────────┘"
echo ""
echo "┌─────────────────────────────────────────────────────────┐"
echo "│  USUARIO REGULAR 2                                      │"
echo "├─────────────────────────────────────────────────────────┤"
echo "│  Email:      john@example.com                           │"
echo "│  Contraseña: password                                   │"
echo "│  Rol:        user                                       │"
echo "│  Permisos:   Crear, Editar productos (NO eliminar)      │"
echo "└─────────────────────────────────────────────────────────┘"
echo ""

# Paso 5: Instrucciones finales
print_success "¡Instalación completada!"
echo ""
echo "📚 Próximos pasos:"
echo ""
echo "1. Iniciar el servidor:"
echo "   ${YELLOW}php artisan serve${NC}"
echo ""
echo "2. Acceder a la aplicación:"
echo "   ${YELLOW}http://127.0.0.1:8000${NC}"
echo ""
echo "3. Hacer login con uno de los usuarios de prueba"
echo ""
echo "4. Ir a productos:"
echo "   ${YELLOW}http://127.0.0.1:8000/products${NC}"
echo ""
echo "5. Observar las diferencias entre admin y user"
echo ""
echo "📖 Documentación:"
echo "   - ${YELLOW}SECURITY_GUIDE.md${NC} - Guía completa de seguridad"
echo "   - ${YELLOW}INSTALLATION_INSTRUCTIONS.md${NC} - Instrucciones detalladas"
echo "   - ${YELLOW}CHANGES_SUMMARY.md${NC} - Resumen de cambios"
echo ""
print_success "¡Listo para usar! 🎉"
