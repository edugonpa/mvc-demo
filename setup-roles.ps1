# Script de instalación del sistema de roles
# Laravel 12 - Sistema de Autenticación y Autorización
# PowerShell Script para Windows

Write-Host "🚀 Instalando sistema de roles..." -ForegroundColor Cyan
Write-Host ""

# Verificar que estamos en un proyecto Laravel
if (-Not (Test-Path "artisan")) {
    Write-Host "❌ Este script debe ejecutarse desde la raíz del proyecto Laravel" -ForegroundColor Red
    exit 1
}

Write-Host "✅ Proyecto Laravel detectado" -ForegroundColor Green
Write-Host ""

# Paso 1: Ejecutar migraciones
Write-Host "📦 Paso 1: Ejecutando migraciones..." -ForegroundColor Yellow
php artisan migrate

if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Migraciones ejecutadas correctamente" -ForegroundColor Green
} else {
    Write-Host "❌ Error al ejecutar migraciones" -ForegroundColor Red
    exit 1
}
Write-Host ""

# Paso 2: Ejecutar seeders
Write-Host "🌱 Paso 2: Ejecutando seeders..." -ForegroundColor Yellow
php artisan db:seed --class=UserSeeder

if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Seeders ejecutados correctamente" -ForegroundColor Green
} else {
    Write-Host "❌ Error al ejecutar seeders" -ForegroundColor Red
    exit 1
}
Write-Host ""

# Paso 3: Limpiar caché
Write-Host "🧹 Paso 3: Limpiando caché..." -ForegroundColor Yellow
php artisan cache:clear | Out-Null
php artisan config:clear | Out-Null
php artisan route:clear | Out-Null
php artisan view:clear | Out-Null
Write-Host "✅ Caché limpiado" -ForegroundColor Green
Write-Host ""

# Paso 4: Mostrar información de usuarios
Write-Host "👥 Usuarios de prueba creados:" -ForegroundColor Cyan
Write-Host ""
Write-Host "┌─────────────────────────────────────────────────────────┐" -ForegroundColor White
Write-Host "│  ADMINISTRADOR                                          │" -ForegroundColor White
Write-Host "├─────────────────────────────────────────────────────────┤" -ForegroundColor White
Write-Host "│  Email:      admin@example.com                          │" -ForegroundColor White
Write-Host "│  Contraseña: password                                   │" -ForegroundColor White
Write-Host "│  Rol:        admin                                      │" -ForegroundColor White
Write-Host "│  Permisos:   Crear, Editar, Eliminar productos          │" -ForegroundColor White
Write-Host "└─────────────────────────────────────────────────────────┘" -ForegroundColor White
Write-Host ""
Write-Host "┌─────────────────────────────────────────────────────────┐" -ForegroundColor White
Write-Host "│  USUARIO REGULAR                                        │" -ForegroundColor White
Write-Host "├─────────────────────────────────────────────────────────┤" -ForegroundColor White
Write-Host "│  Email:      user@example.com                           │" -ForegroundColor White
Write-Host "│  Contraseña: password                                   │" -ForegroundColor White
Write-Host "│  Rol:        user                                       │" -ForegroundColor White
Write-Host "│  Permisos:   Crear, Editar productos (NO eliminar)      │" -ForegroundColor White
Write-Host "└─────────────────────────────────────────────────────────┘" -ForegroundColor White
Write-Host ""
Write-Host "┌─────────────────────────────────────────────────────────┐" -ForegroundColor White
Write-Host "│  USUARIO REGULAR 2                                      │" -ForegroundColor White
Write-Host "├─────────────────────────────────────────────────────────┤" -ForegroundColor White
Write-Host "│  Email:      john@example.com                           │" -ForegroundColor White
Write-Host "│  Contraseña: password                                   │" -ForegroundColor White
Write-Host "│  Rol:        user                                       │" -ForegroundColor White
Write-Host "│  Permisos:   Crear, Editar productos (NO eliminar)      │" -ForegroundColor White
Write-Host "└─────────────────────────────────────────────────────────┘" -ForegroundColor White
Write-Host ""

# Paso 5: Instrucciones finales
Write-Host "✅ ¡Instalación completada!" -ForegroundColor Green
Write-Host ""
Write-Host "📚 Próximos pasos:" -ForegroundColor Cyan
Write-Host ""
Write-Host "1. Iniciar el servidor:" -ForegroundColor White
Write-Host "   php artisan serve" -ForegroundColor Yellow
Write-Host ""
Write-Host "2. Acceder a la aplicación:" -ForegroundColor White
Write-Host "   http://127.0.0.1:8000" -ForegroundColor Yellow
Write-Host ""
Write-Host "3. Hacer login con uno de los usuarios de prueba" -ForegroundColor White
Write-Host ""
Write-Host "4. Ir a productos:" -ForegroundColor White
Write-Host "   http://127.0.0.1:8000/products" -ForegroundColor Yellow
Write-Host ""
Write-Host "5. Observar las diferencias entre admin y user" -ForegroundColor White
Write-Host ""
Write-Host "📖 Documentación:" -ForegroundColor Cyan
Write-Host "   - SECURITY_GUIDE.md - Guía completa de seguridad" -ForegroundColor Yellow
Write-Host "   - INSTALLATION_INSTRUCTIONS.md - Instrucciones detalladas" -ForegroundColor Yellow
Write-Host "   - CHANGES_SUMMARY.md - Resumen de cambios" -ForegroundColor Yellow
Write-Host ""
Write-Host "✅ ¡Listo para usar! 🎉" -ForegroundColor Green
