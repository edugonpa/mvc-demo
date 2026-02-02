# 🔐 Sistema de Autenticación y Autorización con Roles

## Laravel 12 + Breeze - Implementación Completa

Este proyecto implementa un sistema completo de **autenticación** y **autorización basado en roles** (admin/user) con todas las mejores prácticas de seguridad.

---

## 🎯 Características Implementadas

✅ **Sistema de roles** (admin/user)  
✅ **Middleware personalizado** para control de acceso  
✅ **Protección de rutas** según roles  
✅ **Mensajes flash** para feedback al usuario  
✅ **Seguridad CSRF** en todos los formularios  
✅ **Encriptación de contraseñas** con Hash  
✅ **Escape automático** de datos en vistas (XSS protection)  
✅ **Validación de datos** en Form Requests  
✅ **Múltiples capas de seguridad**  

---

## 🚀 Instalación Rápida

### Opción 1: Script Automático (Recomendado)

**Linux/Mac**:
```bash
chmod +x setup-roles.sh
./setup-roles.sh
```

**Windows (PowerShell)**:
```powershell
.\setup-roles.ps1
```

### Opción 2: Manual

```bash
# 1. Ejecutar migraciones
php artisan migrate

# 2. Ejecutar seeders
php artisan db:seed --class=UserSeeder

# 3. Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 4. Iniciar servidor
php artisan serve
```

---

## 👥 Usuarios de Prueba

Después de ejecutar los seeders, tendrás estos usuarios:

| Email | Contraseña | Rol | Permisos |
|-------|-----------|-----|----------|
| **admin@example.com** | password | admin | ✅ Crear, Editar, **Eliminar** productos |
| **user@example.com** | password | user | ✅ Crear, Editar productos<br>❌ NO eliminar |
| **john@example.com** | password | user | ✅ Crear, Editar productos<br>❌ NO eliminar |

---

## 📁 Archivos Creados

### Código
- `database/migrations/2026_02_01_000000_add_role_to_users_table.php` - Migración de roles
- `app/Http/Middleware/RoleMiddleware.php` - Middleware de autorización
- `database/seeders/UserSeeder.php` - Usuarios de prueba

### Documentación
- `SECURITY_GUIDE.md` - **Guía completa de seguridad** (explicación detallada para estudiantes)
- `INSTALLATION_INSTRUCTIONS.md` - Instrucciones de instalación paso a paso
- `CHANGES_SUMMARY.md` - Resumen de todos los cambios realizados
- `README_ROLES.md` - Este archivo

### Scripts
- `setup-roles.sh` - Script de instalación para Linux/Mac
- `setup-roles.ps1` - Script de instalación para Windows

---

## 📝 Archivos Modificados

- `app/Models/User.php` - Agregado campo `role` y métodos helper
- `bootstrap/app.php` - Registrado middleware `role`
- `routes/web.php` - Rutas protegidas por roles
- `resources/views/products/index.blade.php` - Mensajes flash y botón condicional
- `resources/views/dashboard.blade.php` - Mensajes flash y mostrar rol
- `database/seeders/DatabaseSeeder.php` - Incluye UserSeeder

---

## 🔒 Seguridad Implementada

### 1. Protección CSRF
Todos los formularios incluyen `@csrf` para prevenir ataques Cross-Site Request Forgery.

### 2. Encriptación de Contraseñas
Todas las contraseñas se encriptan con `Hash::make()` usando bcrypt.

### 3. Escape de Datos (XSS Protection)
Todas las vistas usan `{{ }}` para escape automático de HTML.

### 4. Middleware de Roles
Control de acceso en múltiples capas:
- **Ruta**: `->middleware('role:admin')`
- **Vista**: `@if(auth()->user()->isAdmin())`
- **Controlador**: Protegido por middleware

### 5. Validación de Datos
Form Requests validan todos los datos de entrada.

---

## 🎓 Documentación para Estudiantes

### Conceptos Clave

**Autenticación**: Verificar **quién** es el usuario (login)  
**Autorización**: Verificar **qué puede hacer** el usuario (permisos)  
**Middleware**: Filtro que se ejecuta antes del controlador  
**Roles**: Categorías de usuarios con diferentes permisos  

### Flujo de una Petición

```
Usuario → Middleware auth → Middleware role → Controlador → Vista
          ↓                 ↓
    ¿Está logueado?    ¿Tiene el rol?
```

### Ejemplo Práctico

```
Usuario regular intenta eliminar producto:
1. Middleware 'auth' verifica que esté logueado ✓
2. Middleware 'role:admin' verifica que sea admin ✗
3. Redirige a dashboard con mensaje de error
4. Nunca llega al controlador
```

---

## 🧪 Pruebas

### Prueba 1: Usuario Regular
1. Login con `user@example.com` / `password`
2. Ir a `/products`
3. **Resultado**: Puede ver, crear y editar, pero NO ve botón de eliminar

### Prueba 2: Usuario Admin
1. Login con `admin@example.com` / `password`
2. Ir a `/products`
3. **Resultado**: Puede ver, crear, editar y eliminar productos

### Prueba 3: Intento de Bypass
1. Login como `user@example.com`
2. Intentar eliminar manualmente (ej: con Postman):
   ```
   DELETE http://127.0.0.1:8000/products/1
   ```
3. **Resultado**: Redirige a dashboard con mensaje "No tienes permisos..."

---

## 📚 Documentación Completa

Para entender en detalle cómo funciona cada componente, lee:

### 1. SECURITY_GUIDE.md
**Guía completa de seguridad** con:
- Explicación paso a paso de cada componente
- Conceptos de autenticación y autorización
- Cómo funciona el middleware
- Medidas de seguridad implementadas
- Preguntas frecuentes
- Mejores prácticas

### 2. INSTALLATION_INSTRUCTIONS.md
**Instrucciones de instalación** con:
- Pasos detallados de instalación
- Cómo crear usuarios adicionales
- Solución de problemas comunes
- Comandos útiles

### 3. CHANGES_SUMMARY.md
**Resumen de cambios** con:
- Lista de archivos nuevos y modificados
- Comparación antes vs después
- Checklist de verificación
- Estructura de archivos

---

## 🛠️ Comandos Útiles

### Ver rutas
```bash
php artisan route:list
```

### Crear usuario manualmente
```bash
php artisan tinker
```
```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Nuevo Admin',
    'email' => 'nuevo@example.com',
    'password' => Hash::make('password'),
    'role' => 'admin',
    'email_verified_at' => now(),
]);
```

### Cambiar rol de un usuario
```bash
php artisan tinker
```
```php
$user = User::where('email', 'user@example.com')->first();
$user->role = 'admin';
$user->save();
```

### Resetear base de datos
```bash
php artisan migrate:fresh --seed
```
**⚠️ ADVERTENCIA**: Esto borra TODA la base de datos.

---

## 🎯 Funcionalidades por Rol

### Usuario Regular (role: user)
- ✅ Login/Logout
- ✅ Ver dashboard
- ✅ Ver lista de productos
- ✅ Crear productos
- ✅ Editar productos
- ❌ **NO puede eliminar productos**

### Usuario Administrador (role: admin)
- ✅ Login/Logout
- ✅ Ver dashboard
- ✅ Ver lista de productos
- ✅ Crear productos
- ✅ Editar productos
- ✅ **Eliminar productos**

---

## 🔧 Personalización

### Agregar más roles

1. **Modificar el middleware** para aceptar el nuevo rol:
```php
// routes/web.php
->middleware('role:admin,moderator')
```

2. **Agregar método helper en User**:
```php
// app/Models/User.php
public function isModerator(): bool
{
    return $this->role === 'moderator';
}
```

3. **Usar en vistas**:
```blade
@if(auth()->user()->isModerator())
    <!-- contenido para moderadores -->
@endif
```

### Proteger más rutas

```php
// routes/web.php
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index']);
    Route::resource('/admin/users', UserController::class);
});
```

---

## ❓ Preguntas Frecuentes

### ¿Por qué no usar Spatie Permission?
Para proyectos pequeños con 2-3 roles, un middleware personalizado es más simple y educativo. Spatie Permission es excelente para sistemas complejos.

### ¿Es seguro guardar el rol en la tabla users?
Sí, para sistemas simples. Para sistemas complejos, considera una tabla separada `roles` con relación many-to-many.

### ¿Qué pasa si cambio el rol mientras el usuario está logueado?
El cambio se reflejará en la siguiente petición. Laravel carga los datos del usuario en cada petición.

### ¿Cómo protejo rutas API?
Usa el mismo middleware con Sanctum:
```php
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::delete('/api/products/{product}', [ProductController::class, 'destroy']);
});
```

---

## 📊 Comparación

### ANTES
❌ Todos los usuarios podían eliminar productos  
❌ No había control de roles  
❌ No había mensajes flash  

### DESPUÉS
✅ Solo admins pueden eliminar productos  
✅ Sistema de roles completo  
✅ Mensajes flash de éxito y error  
✅ Múltiples capas de seguridad  
✅ Documentación completa  

---

## 🎉 Resultado

Un sistema completo de autenticación y autorización que:

1. ✅ **Funciona** - Implementación completa y probada
2. ✅ **Es seguro** - Múltiples capas de protección
3. ✅ **Es educativo** - Documentación detallada
4. ✅ **Es mantenible** - Código limpio y estructurado
5. ✅ **Es escalable** - Fácil agregar más roles

**Sin paquetes externos** - Solo Laravel y Breeze  
**Sin cambios arquitectónicos** - Respeta la estructura existente  

---

## 📞 Soporte

Para más información, consulta:
- `SECURITY_GUIDE.md` - Guía completa
- `INSTALLATION_INSTRUCTIONS.md` - Instalación detallada
- `CHANGES_SUMMARY.md` - Resumen de cambios

---

**Versión**: 1.0.0  
**Laravel**: 12.47.0  
**PHP**: 8.5.2  
**Fecha**: 2026-02-01  
**Estado**: ✅ Listo para producción

---

## 📄 Licencia

Este código es parte de un proyecto educativo y puede ser usado libremente para aprendizaje y desarrollo.

---

**¡Listo para usar!** 🚀
