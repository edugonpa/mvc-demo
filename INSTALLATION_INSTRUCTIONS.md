# 🚀 Instrucciones de Instalación - Sistema de Roles

## Pasos Rápidos

### 1. Ejecutar la migración para agregar el campo `role`

```bash
php artisan migrate
```

Esto agregará la columna `role` a la tabla `users`.

---

### 2. Ejecutar los seeders para crear usuarios de prueba

```bash
php artisan db:seed --class=UserSeeder
```

O si quieres resetear toda la base de datos:

```bash
php artisan migrate:fresh --seed
```

**⚠️ ADVERTENCIA**: `migrate:fresh` borra TODA la base de datos.

---

### 3. Usuarios de prueba creados

Después de ejecutar el seeder, tendrás estos usuarios:

| Email | Contraseña | Rol | Permisos |
|-------|-----------|-----|----------|
| admin@example.com | password | admin | Puede crear, editar y **eliminar** productos |
| user@example.com | password | user | Puede crear y editar productos (NO eliminar) |
| john@example.com | password | user | Puede crear y editar productos (NO eliminar) |

---

### 4. Probar el sistema

1. **Iniciar el servidor**:
   ```bash
   php artisan serve
   ```

2. **Acceder a la aplicación**:
   ```
   http://127.0.0.1:8000
   ```

3. **Hacer login**:
   - Ve a `/login`
   - Prueba con `admin@example.com` / `password`
   - Luego prueba con `user@example.com` / `password`

4. **Ir a productos**:
   ```
   http://127.0.0.1:8000/products
   ```

5. **Observar diferencias**:
   - Como **admin**: Verás el botón "🗑 Eliminar"
   - Como **user**: NO verás el botón de eliminar

---

## 🧪 Pruebas de Seguridad

### Prueba 1: Usuario regular intenta eliminar

1. Login como `user@example.com`
2. Ir a `/products`
3. Intentar eliminar un producto manualmente (usando herramientas de desarrollador o Postman):
   ```
   DELETE http://127.0.0.1:8000/products/1
   ```
4. **Resultado esperado**: Redirige a dashboard con mensaje "No tienes permisos para realizar esta acción."

### Prueba 2: Usuario admin puede eliminar

1. Login como `admin@example.com`
2. Ir a `/products`
3. Click en "🗑 Eliminar"
4. **Resultado esperado**: Producto eliminado, mensaje "Producto eliminado correctamente."

---

## 📝 Crear Usuarios Adicionales

### Opción 1: Usando Tinker

```bash
php artisan tinker
```

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Nuevo Usuario',
    'email' => 'nuevo@example.com',
    'password' => Hash::make('password'),
    'role' => 'user', // o 'admin'
    'email_verified_at' => now(),
]);
```

### Opción 2: Registro normal

1. Ve a `/register`
2. Completa el formulario
3. El usuario se creará con rol `user` por defecto
4. Para hacerlo admin, edita manualmente en la base de datos o usa Tinker:

```bash
php artisan tinker
```

```php
$user = User::where('email', 'email@example.com')->first();
$user->role = 'admin';
$user->save();
```

---

## 🔧 Solución de Problemas

### Error: "Column 'role' not found"

**Solución**: Ejecuta la migración
```bash
php artisan migrate
```

### Error: "Class 'UserSeeder' not found"

**Solución**: Regenera el autoload
```bash
composer dump-autoload
php artisan db:seed --class=UserSeeder
```

### Los cambios no se reflejan

**Solución**: Limpia el caché
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 📚 Archivos Modificados

### Nuevos archivos creados:
- `database/migrations/2026_02_01_000000_add_role_to_users_table.php`
- `app/Http/Middleware/RoleMiddleware.php`
- `database/seeders/UserSeeder.php`
- `SECURITY_GUIDE.md`
- `INSTALLATION_INSTRUCTIONS.md`

### Archivos modificados:
- `app/Models/User.php` - Agregado campo `role` y métodos helper
- `bootstrap/app.php` - Registrado middleware `role`
- `routes/web.php` - Rutas protegidas por roles
- `resources/views/products/index.blade.php` - Mensajes flash y botón condicional
- `resources/views/dashboard.blade.php` - Mensajes flash y mostrar rol
- `database/seeders/DatabaseSeeder.php` - Incluye UserSeeder

---

## 🎯 Siguiente Paso

Lee el archivo **SECURITY_GUIDE.md** para entender en detalle:
- Cómo funciona cada componente
- Mejores prácticas de seguridad
- Explicaciones paso a paso para estudiantes
- Preguntas frecuentes

---

**¡Listo para usar!** 🎉
