# ✅ Checklist de Verificación - Sistema de Roles

## 📋 Antes de Empezar

- [ ] Tienes Laravel 12 instalado
- [ ] Tienes Laravel Breeze instalado
- [ ] La base de datos está configurada en `.env`
- [ ] Puedes ejecutar `php artisan migrate`

---

## 🚀 Instalación

### Paso 1: Ejecutar Migración
```bash
php artisan migrate
```

- [ ] La migración se ejecutó sin errores
- [ ] La tabla `users` ahora tiene la columna `role`

**Verificar**:
```bash
php artisan tinker
```
```php
Schema::hasColumn('users', 'role')  // Debe devolver true
```

---

### Paso 2: Ejecutar Seeders
```bash
php artisan db:seed --class=UserSeeder
```

- [ ] El seeder se ejecutó sin errores
- [ ] Se crearon 3 usuarios

**Verificar**:
```bash
php artisan tinker
```
```php
User::count()  // Debe devolver al menos 3
User::where('role', 'admin')->count()  // Debe devolver al menos 1
```

---

### Paso 3: Limpiar Caché
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

- [ ] Todos los comandos se ejecutaron sin errores

---

## 🧪 Pruebas Funcionales

### Prueba 1: Login como Admin

1. Iniciar servidor:
```bash
php artisan serve
```

2. Acceder a: `http://127.0.0.1:8000/login`

3. Login con:
   - Email: `admin@example.com`
   - Contraseña: `password`

- [ ] El login fue exitoso
- [ ] Redirige al dashboard
- [ ] El dashboard muestra "Tu rol: admin"

---

### Prueba 2: Admin puede eliminar productos

1. Ir a: `http://127.0.0.1:8000/products`

- [ ] Se muestra la lista de productos
- [ ] Hay un botón "➕ Crear producto"
- [ ] Cada producto tiene un botón "✏️ Editar"
- [ ] Cada producto tiene un botón "🗑 Eliminar" ← **IMPORTANTE**

2. Click en "🗑 Eliminar" en algún producto

- [ ] Aparece confirmación JavaScript
- [ ] Al confirmar, el producto se elimina
- [ ] Aparece mensaje verde: "Producto eliminado correctamente."

---

### Prueba 3: Login como Usuario Regular

1. Logout (si estás logueado)

2. Login con:
   - Email: `user@example.com`
   - Contraseña: `password`

- [ ] El login fue exitoso
- [ ] Redirige al dashboard
- [ ] El dashboard muestra "Tu rol: user"

---

### Prueba 4: Usuario Regular NO puede eliminar

1. Ir a: `http://127.0.0.1:8000/products`

- [ ] Se muestra la lista de productos
- [ ] Hay un botón "➕ Crear producto"
- [ ] Cada producto tiene un botón "✏️ Editar"
- [ ] **NO hay botón "🗑 Eliminar"** ← **IMPORTANTE**

---

### Prueba 5: Usuario Regular puede crear

1. Click en "➕ Crear producto"

- [ ] Se muestra el formulario de creación
- [ ] Puedes completar el formulario
- [ ] Al enviar, el producto se crea
- [ ] Aparece mensaje verde: "Producto creado."

---

### Prueba 6: Usuario Regular puede editar

1. Click en "✏️ Editar" en algún producto

- [ ] Se muestra el formulario de edición
- [ ] Puedes modificar los datos
- [ ] Al enviar, el producto se actualiza
- [ ] Aparece mensaje verde: "Producto actualizado."

---

### Prueba 7: Intento de Bypass (Seguridad)

1. Estando logueado como `user@example.com`

2. Intentar eliminar manualmente usando herramientas de desarrollador:
   - Abrir consola del navegador (F12)
   - Ir a Network
   - Enviar petición DELETE a `/products/1`

**O usar Postman/cURL**:
```bash
curl -X DELETE http://127.0.0.1:8000/products/1 \
  -H "Cookie: laravel_session=TU_SESSION_COOKIE"
```

- [ ] La petición es rechazada
- [ ] Redirige al dashboard
- [ ] Aparece mensaje rojo: "No tienes permisos para realizar esta acción."

---

## 🔒 Verificación de Seguridad

### CSRF Protection

1. Inspeccionar formularios en:
   - `/products/create`
   - `/products/{id}/edit`
   - Botón de eliminar en `/products`

- [ ] Todos tienen campo oculto `_token`
- [ ] El token es diferente en cada sesión

**Verificar en HTML**:
```html
<input type="hidden" name="_token" value="...">
```

---

### Hash de Contraseñas

1. Verificar en la base de datos:
```bash
php artisan tinker
```
```php
$user = User::first();
echo $user->password;
```

- [ ] La contraseña NO es texto plano
- [ ] Empieza con `$2y$` (bcrypt)
- [ ] Tiene al menos 60 caracteres

**Ejemplo correcto**: `$2y$10$abc123xyz...`  
**Ejemplo incorrecto**: `password` ❌

---

### Blade Escaping

1. Crear un producto con nombre malicioso:
```
<script>alert('XSS')</script>
```

2. Ver la lista de productos

- [ ] El script NO se ejecuta
- [ ] Se muestra como texto: `<script>alert('XSS')</script>`
- [ ] No aparece un alert en el navegador

---

### Middleware Registrado

1. Verificar rutas:
```bash
php artisan route:list | grep products
```

- [ ] La ruta `DELETE /products/{product}` tiene middleware `role:admin`
- [ ] Las demás rutas tienen middleware `auth`

---

## 📁 Verificación de Archivos

### Archivos Nuevos

- [ ] `database/migrations/2026_02_01_000000_add_role_to_users_table.php` existe
- [ ] `app/Http/Middleware/RoleMiddleware.php` existe
- [ ] `database/seeders/UserSeeder.php` existe
- [ ] `SECURITY_GUIDE.md` existe
- [ ] `INSTALLATION_INSTRUCTIONS.md` existe
- [ ] `CHANGES_SUMMARY.md` existe
- [ ] `README_ROLES.md` existe
- [ ] `setup-roles.sh` existe
- [ ] `setup-roles.ps1` existe
- [ ] `CHECKLIST.md` existe (este archivo)

---

### Archivos Modificados

- [ ] `app/Models/User.php` tiene campo `role` en `$fillable`
- [ ] `app/Models/User.php` tiene métodos `isAdmin()`, `isUser()`, `hasRole()`
- [ ] `bootstrap/app.php` registra middleware `role`
- [ ] `routes/web.php` tiene rutas protegidas por roles
- [ ] `resources/views/products/index.blade.php` tiene mensajes flash
- [ ] `resources/views/products/index.blade.php` tiene botón condicional
- [ ] `resources/views/dashboard.blade.php` tiene mensajes flash
- [ ] `database/seeders/DatabaseSeeder.php` llama a `UserSeeder`

---

## 🎯 Verificación de Funcionalidades

### Roles

- [ ] Los usuarios tienen campo `role` en la base de datos
- [ ] El rol por defecto es `'user'`
- [ ] Se pueden crear usuarios con rol `'admin'`

### Permisos

| Acción | Admin | User |
|--------|-------|------|
| Ver productos | ✅ | ✅ |
| Crear productos | ✅ | ✅ |
| Editar productos | ✅ | ✅ |
| Eliminar productos | ✅ | ❌ |

- [ ] Todos los permisos funcionan correctamente

### Mensajes Flash

- [ ] Aparecen mensajes de éxito (verde) al crear/editar/eliminar
- [ ] Aparecen mensajes de error (rojo) al intentar acción sin permisos
- [ ] Los mensajes desaparecen después de mostrarse una vez

---

## 📚 Verificación de Documentación

- [ ] `SECURITY_GUIDE.md` explica todos los conceptos
- [ ] `INSTALLATION_INSTRUCTIONS.md` tiene pasos claros
- [ ] `CHANGES_SUMMARY.md` lista todos los cambios
- [ ] `README_ROLES.md` tiene resumen completo
- [ ] Todos los archivos están en formato Markdown
- [ ] Todos los archivos tienen ejemplos de código

---

## 🐛 Solución de Problemas

### Error: "Column 'role' not found"

**Solución**:
```bash
php artisan migrate
```

- [ ] Problema resuelto

---

### Error: "Class 'UserSeeder' not found"

**Solución**:
```bash
composer dump-autoload
php artisan db:seed --class=UserSeeder
```

- [ ] Problema resuelto

---

### Error: "Middleware [role] not found"

**Solución**:
```bash
php artisan config:clear
php artisan route:clear
```

- [ ] Problema resuelto

---

### Los cambios no se reflejan

**Solución**:
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

- [ ] Problema resuelto

---

## ✅ Checklist Final

### Funcionalidad
- [ ] Los usuarios pueden hacer login
- [ ] Los admins pueden eliminar productos
- [ ] Los usuarios regulares NO pueden eliminar productos
- [ ] Los mensajes flash funcionan correctamente
- [ ] La confirmación de eliminación funciona

### Seguridad
- [ ] Todos los formularios tienen `@csrf`
- [ ] Todas las contraseñas están hasheadas
- [ ] Todas las vistas usan `{{ }}` para datos
- [ ] El middleware de roles funciona
- [ ] No se puede hacer bypass de permisos

### Documentación
- [ ] Todos los archivos de documentación existen
- [ ] La documentación es clara y completa
- [ ] Hay ejemplos de código
- [ ] Hay instrucciones de instalación

### Código
- [ ] No hay errores de sintaxis
- [ ] El código sigue las convenciones de Laravel
- [ ] Los comentarios son claros
- [ ] No hay código duplicado

---

## 🎉 ¡Completado!

Si todas las casillas están marcadas, el sistema está funcionando correctamente.

**Próximos pasos**:
1. Leer `SECURITY_GUIDE.md` para entender en profundidad
2. Personalizar según tus necesidades
3. Agregar más roles si es necesario
4. Implementar en producción

---

**Fecha de verificación**: _____________  
**Verificado por**: _____________  
**Estado**: ⬜ Pendiente | ⬜ En progreso | ⬜ Completado

---

**¡Éxito!** 🚀
