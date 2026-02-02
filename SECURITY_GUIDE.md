# Guía de Seguridad - Sistema de Autenticación y Autorización con Laravel Breeze

## 📋 Índice
1. [Introducción](#introducción)
2. [Conceptos Clave](#conceptos-clave)
3. [Implementación Paso a Paso](#implementación-paso-a-paso)
4. [Medidas de Seguridad Implementadas](#medidas-de-seguridad-implementadas)
5. [Pruebas y Validación](#pruebas-y-validación)
6. [Preguntas Frecuentes](#preguntas-frecuentes)
7. [Mejores Prácticas](#mejores-prácticas)

---

## 🎯 Introducción

Este documento explica paso a paso cómo se implementó un sistema de **autenticación** y **autorización basado en roles** en Laravel 12 con Breeze, enfocándose en las mejores prácticas de seguridad.

### ¿Qué se implementó?

✅ Sistema de roles (admin/user)  
✅ Middleware personalizado para control de acceso  
✅ Protección de rutas según roles  
✅ Mensajes flash para feedback al usuario  
✅ Seguridad en formularios con CSRF  
✅ Encriptación de contraseñas con Hash  
✅ Escape automático de datos en vistas  

---

## 🔑 Conceptos Clave

### 1. Autenticación vs Autorización

**Autenticación**: Verificar **quién** es el usuario (login)
- ¿El usuario es quien dice ser?
- Laravel Breeze maneja esto automáticamente

**Autorización**: Verificar **qué puede hacer** el usuario (permisos)
- ¿El usuario tiene permiso para realizar esta acción?
- Implementamos esto con middleware de roles

### 2. Middleware

Un middleware es un "filtro" que se ejecuta **antes** de que una petición llegue al controlador.

```
Usuario → Middleware → Controlador → Vista
          ↓
    ¿Tiene permiso?
```

### 3. Roles

En este sistema tenemos dos roles:
- **admin**: Puede hacer todo (crear, editar, eliminar productos)
- **user**: Puede crear y editar, pero NO eliminar

---

## 🛠️ Implementación Paso a Paso

### PASO 1: Agregar columna `role` a la tabla users

php artisan make:migration add_role_to_users_table --table=users

**Archivo**: `database/migrations/2026_02_01_000000_add_role_to_users_table.php`

```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('role')->default('user')->after('email');
    });
}
```

**¿Por qué?**
- Necesitamos almacenar el rol de cada usuario en la base de datos
- Por defecto, todos los usuarios nuevos serán 'user'
- El campo se coloca después de 'email' para mantener orden lógico

**Ejecutar migración**:
```bash
php artisan migrate
```

---

### PASO 2: Actualizar el modelo User

**Archivo**: `app/Models/User.php`

**Cambio 1**: Agregar 'role' a $fillable
```php
protected $fillable = [
    'name',
    'email',
    'password',
    'role',  // ← NUEVO
];
```

**¿Por qué?**
- Laravel protege contra asignación masiva por seguridad
- Solo los campos en $fillable pueden ser asignados con `User::create()`

**Cambio 2**: Agregar métodos helper
```php
public function hasRole(string $role): bool
{
    return $this->role === $role;
}

public function isAdmin(): bool
{
    return $this->role === 'admin';
}

public function isUser(): bool
{
    return $this->role === 'user';
}
```

**¿Por qué?**
- Facilita verificar roles en el código
- Más legible: `$user->isAdmin()` vs `$user->role === 'admin'`
- Si cambiamos la lógica de roles, solo modificamos estos métodos

---

### PASO 3: Crear el Middleware de Roles

php artisan make:middleware RoleMiddleware

**Archivo**: `app/Http/Middleware/RoleMiddleware.php`

```php
public function handle(Request $request, Closure $next, string ...$roles): Response
{
    // 1. Verificar que el usuario esté autenticado
    if (!auth()->check()) {
        return redirect()->route('login')
            ->with('error', 'Debes iniciar sesión para acceder a esta página.');
    }

    // 2. Obtener el usuario autenticado
    $user = auth()->user();

    // 3. Verificar si el usuario tiene alguno de los roles permitidos
    if (!in_array($user->role, $roles)) {
        return redirect()->route('dashboard')
            ->with('error', 'No tienes permisos para realizar esta acción.');
    }

    // 4. Si tiene el rol correcto, continuar con la petición
    return $next($request);
}
```

**Explicación línea por línea**:

1. **`auth()->check()`**: Verifica si hay un usuario autenticado
   - Si no hay usuario, redirige al login

2. **`auth()->user()`**: Obtiene el usuario actual
   - Devuelve el objeto User completo

3. **`in_array($user->role, $roles)`**: Verifica si el rol del usuario está en la lista de roles permitidos
   - `$roles` es un array: `['admin']` o `['admin', 'user']`
   - Permite flexibilidad: una ruta puede requerir múltiples roles

4. **`return $next($request)`**: Permite que la petición continúe
   - Es como decir "OK, puedes pasar"

**Mensajes flash**:
- `->with('error', 'mensaje')`: Guarda un mensaje en la sesión
- Se muestra una sola vez y luego se elimina automáticamente

---

### PASO 4: Registrar el Middleware

**Archivo**: `bootstrap/app.php`

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);
})
```

**¿Por qué?**
- Registra un "alias" para el middleware
- Ahora podemos usar `->middleware('role:admin')` en las rutas
- Sin esto, Laravel no sabría qué es 'role'

**Nota**: En Laravel 12, los middlewares se registran en `bootstrap/app.php`, no en `Kernel.php` (como en versiones anteriores).

---

### PASO 5: Proteger las Rutas

**Archivo**: `routes/web.php`

**Antes** (sin protección de roles):
```php
Route::resource('products', ProductController::class)->middleware('auth');
```

**Después** (con protección de roles):
```php
Route::middleware(['auth'])->group(function () {
    // Listar - Todos los usuarios autenticados
    Route::get('/products', [ProductController::class, 'index'])
        ->name('products.index');
    
    // Crear - Todos los usuarios autenticados
    Route::get('/products/create', [ProductController::class, 'create'])
        ->name('products.create');
    
    Route::post('/products', [ProductController::class, 'store'])
        ->name('products.store');
    
    // Editar - Todos los usuarios autenticados
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])
        ->name('products.edit');
    
    Route::put('/products/{product}', [ProductController::class, 'update'])
        ->name('products.update');
    
    // Eliminar - SOLO ADMIN
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])
        ->middleware('role:admin')  // ← PROTECCIÓN ADICIONAL
        ->name('products.destroy');
});
```

**¿Por qué separar las rutas?**
- Más control granular sobre cada acción
- Solo la ruta DELETE tiene protección adicional de rol
- Más fácil de entender y mantener

**Flujo de protección**:
```
1. Usuario intenta eliminar producto
2. Middleware 'auth' verifica que esté autenticado
3. Middleware 'role:admin' verifica que sea admin
4. Si pasa ambos, llega al controlador
5. Si falla alguno, redirige con mensaje de error
```

---

### PASO 6: Agregar Mensajes Flash a las Vistas

**Archivo**: `resources/views/products/index.blade.php`

```blade
{{-- Mensajes flash de éxito --}}
@if(session('success'))
    <div style="padding: 10px; margin-bottom: 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 4px;">
        ✅ {{ session('success') }}
    </div>
@endif

{{-- Mensajes flash de error --}}
@if(session('error'))
    <div style="padding: 10px; margin-bottom: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px;">
        ❌ {{ session('error') }}
    </div>
@endif
```

**¿Cómo funcionan?**
1. El controlador o middleware guarda un mensaje: `->with('success', 'Producto creado')`
2. Laravel guarda el mensaje en la sesión
3. La vista verifica si existe: `@if(session('success'))`
4. Si existe, lo muestra
5. Después de mostrarlo, Laravel lo elimina automáticamente

**Tipos de mensajes**:
- `success`: Operación exitosa (verde)
- `error`: Error o falta de permisos (rojo)

---

### PASO 7: Ocultar botón de eliminar para usuarios no-admin

**Archivo**: `resources/views/products/index.blade.php`

```blade
{{-- Solo mostrar botón de eliminar si el usuario es admin --}}
@if(auth()->user()->isAdmin())
    <form action="{{ route('products.destroy', $product) }}"
          method="POST"
          style="display:inline;"
          onsubmit="return confirm('¿Estás seguro de eliminar este producto?');">
        @csrf
        @method('DELETE')
        <button type="submit">🗑 Eliminar</button>
    </form>
@endif
```

**¿Por qué?**
- **Seguridad en capas**: Aunque ocultamos el botón, la ruta sigue protegida
- **Mejor UX**: El usuario no ve opciones que no puede usar
- **Confirmación**: `onsubmit="return confirm()"` previene eliminaciones accidentales

**Importante**: Ocultar el botón NO es suficiente para seguridad. Un usuario malicioso podría enviar la petición DELETE manualmente. Por eso también protegemos la ruta con middleware.

---

## 🔒 Medidas de Seguridad Implementadas

### 1. Protección CSRF (Cross-Site Request Forgery)

**¿Qué es CSRF?**
Un ataque donde un sitio malicioso envía peticiones a tu aplicación usando la sesión del usuario.

**Cómo lo prevenimos**:
```blade
<form action="{{ route('products.store') }}" method="POST">
    @csrf  <!-- ← Token de seguridad -->
    <!-- campos del formulario -->
</form>
```

**¿Cómo funciona?**
1. Laravel genera un token único por sesión
2. `@csrf` inserta un campo oculto con ese token
3. Al enviar el formulario, Laravel verifica que el token sea válido
4. Si no coincide, rechaza la petición

**Resultado en HTML**:
```html
<input type="hidden" name="_token" value="abc123xyz...">
```

---

### 2. Encriptación de Contraseñas con Hash

**NUNCA guardar contraseñas en texto plano**

**Correcto** ✅:
```php
User::create([
    'password' => Hash::make($request->password),
]);
```

**Incorrecto** ❌:
```php
User::create([
    'password' => $request->password,  // ¡PELIGRO!
]);
```

**¿Por qué usar Hash::make?**
- Usa bcrypt (algoritmo seguro)
- Genera un "salt" único por contraseña
- Irreversible: no se puede "desencriptar"
- Cada vez que hasheas la misma contraseña, obtienes un resultado diferente

**Ejemplo**:
```php
Hash::make('password123')  // $2y$10$abc...
Hash::make('password123')  // $2y$10$xyz...  ← Diferente!
```

**Verificación**:
```php
Hash::check('password123', $hashedPassword)  // true o false
```

Laravel hace esto automáticamente en el login.

---

### 3. Blade Escaping (Prevención de XSS)

**¿Qué es XSS (Cross-Site Scripting)?**
Un ataque donde se inyecta código JavaScript malicioso en tu sitio.

**Cómo lo prevenimos**:

**Correcto** ✅ (escape automático):
```blade
<td>{{ $product->nombre }}</td>
```

**Incorrecto** ❌ (sin escape):
```blade
<td>{!! $product->nombre !!}</td>
```

**¿Qué hace `{{ }}`?**
Convierte caracteres especiales en entidades HTML:

```php
// Si $product->nombre = "<script>alert('hack')</script>"

{{ $product->nombre }}
// Resultado: &lt;script&gt;alert('hack')&lt;/script&gt;
// Se muestra como texto, no se ejecuta

{!! $product->nombre !!}
// Resultado: <script>alert('hack')</script>
// ¡SE EJECUTA! ❌
```

**Regla de oro**: Usa `{{ }}` siempre, excepto cuando necesites HTML confiable (ej: contenido de un editor WYSIWYG que ya sanitizaste).

---

### 4. Validación de Datos

**Archivo**: `app/Http/Requests/StoreProductRequest.php`

```php
public function rules(): array
{
    return [
        'nombre' => 'required|string|max:255',
        'precio' => 'required|numeric|min:0',
        'category_id' => 'required|exists:categories,id',
    ];
}
```

**¿Por qué validar?**
- Previene datos maliciosos o incorrectos
- Protege la integridad de la base de datos
- Mejora la experiencia del usuario

**Reglas importantes**:
- `required`: Campo obligatorio
- `string`: Debe ser texto
- `max:255`: Máximo 255 caracteres (previene ataques de buffer)
- `numeric`: Solo números
- `min:0`: No permite precios negativos
- `exists:categories,id`: Verifica que la categoría exista (previene inyección de IDs falsos)

---

### 5. Autorización en Múltiples Capas

**Capa 1: Rutas**
```php
Route::delete('/products/{product}', [ProductController::class, 'destroy'])
    ->middleware('role:admin');
```

**Capa 2: Vista**
```blade
@if(auth()->user()->isAdmin())
    <!-- botón eliminar -->
@endif
```

**Capa 3: Controlador (opcional, pero recomendado)**
```php
public function destroy(Product $product)
{
    // Verificación adicional
    if (!auth()->user()->isAdmin()) {
        abort(403, 'No autorizado');
    }
    
    $product->delete();
    return redirect()->route('products.index')
        ->with('success', 'Producto eliminado correctamente.');
}
```

**¿Por qué múltiples capas?**
- **Defensa en profundidad**: Si una capa falla, las otras protegen
- **Seguridad**: Nunca confíes solo en el frontend
- **Redundancia**: Mejor prevenir que lamentar

---

## 🧪 Pruebas y Validación

### Crear Usuarios de Prueba

**Archivo**: `database/seeders/UserSeeder.php`

```php
// Usuario administrador
User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
    'role' => 'admin',
]);

// Usuario regular
User::create([
    'name' => 'Regular User',
    'email' => 'user@example.com',
    'password' => Hash::make('password'),
    'role' => 'user',
]);
```

**Ejecutar seeders**:
```bash
php artisan migrate:fresh --seed
```

**⚠️ ADVERTENCIA**: `migrate:fresh` borra TODA la base de datos. Úsalo solo en desarrollo.

---

### Escenarios de Prueba

#### Prueba 1: Usuario no autenticado
1. Cerrar sesión
2. Intentar acceder a `/products`
3. **Resultado esperado**: Redirige a `/login`

#### Prueba 2: Usuario regular (role: user)
1. Login con `user@example.com` / `password`
2. Ir a `/products`
3. **Puede**: Ver lista, crear, editar
4. **No puede**: Ver botón de eliminar
5. Intentar eliminar manualmente (ej: con Postman)
6. **Resultado esperado**: Redirige a dashboard con mensaje de error

#### Prueba 3: Usuario administrador (role: admin)
1. Login con `admin@example.com` / `password`
2. Ir a `/products`
3. **Puede**: Ver lista, crear, editar, eliminar
4. **Resultado esperado**: Botón de eliminar visible y funcional

#### Prueba 4: Mensajes flash
1. Crear un producto
2. **Resultado esperado**: Mensaje verde "Producto creado"
3. Eliminar un producto (como admin)
4. **Resultado esperado**: Mensaje verde "Producto eliminado correctamente"
5. Intentar eliminar como user
6. **Resultado esperado**: Mensaje rojo "No tienes permisos..."

---

## ❓ Preguntas Frecuentes

### 1. ¿Por qué no usar un paquete de roles como Spatie Permission?

**Respuesta**: Para proyectos pequeños con solo 2 roles, un middleware personalizado es más simple y educativo. Spatie Permission es excelente para sistemas complejos con muchos roles y permisos.

**Cuándo usar cada uno**:
- **Middleware personalizado**: 2-3 roles simples
- **Spatie Permission**: Múltiples roles, permisos granulares, roles dinámicos

---

### 2. ¿Puedo tener más de 2 roles?

**Sí**, solo agrega más valores al campo `role`:

```php
// Middleware
->middleware('role:admin,moderator')

// Vista
@if(auth()->user()->hasRole('moderator'))
    <!-- contenido para moderadores -->
@endif
```

---

### 3. ¿Qué pasa si cambio el rol de un usuario mientras está logueado?

El cambio se reflejará en la siguiente petición. Laravel carga los datos del usuario en cada petición desde la base de datos.

**Para forzar logout**:
```php
Auth::logout();
```

---

### 4. ¿Es seguro guardar el rol en la tabla users?

**Sí**, para sistemas simples. Para sistemas complejos, considera una tabla separada `roles` con relación many-to-many.

**Ventajas de tabla separada**:
- Roles dinámicos (crear/editar desde admin panel)
- Múltiples roles por usuario
- Permisos granulares

**Ventajas de columna simple**:
- Más simple
- Más rápido (menos JOINs)
- Suficiente para 2-3 roles fijos

---

### 5. ¿Cómo protejo rutas API?

Usa el mismo middleware:

```php
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::delete('/api/products/{product}', [ProductController::class, 'destroy']);
});
```

---

## 🎓 Mejores Prácticas

### 1. Nunca confíes en el frontend

❌ **Incorrecto**:
```blade
@if(auth()->user()->isAdmin())
    <button onclick="deleteProduct()">Eliminar</button>
@endif
```
Sin protección en el backend, un usuario puede llamar `deleteProduct()` desde la consola.

✅ **Correcto**:
```blade
@if(auth()->user()->isAdmin())
    <form action="{{ route('products.destroy', $product) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit">Eliminar</button>
    </form>
@endif
```
Y proteger la ruta con middleware.

---

### 2. Usa Form Requests para validación

❌ **Incorrecto**:
```php
public function store(Request $request)
{
    $request->validate([...]);  // Validación en el controlador
}
```

✅ **Correcto**:
```php
public function store(StoreProductRequest $request)
{
    // Validación automática antes de llegar aquí
}
```

**Ventajas**:
- Controladores más limpios
- Validación reutilizable
- Mensajes de error personalizables

---

### 3. Usa métodos helper en el modelo

❌ **Incorrecto**:
```blade
@if($user->role === 'admin')
```

✅ **Correcto**:
```blade
@if($user->isAdmin())
```

**Ventajas**:
- Más legible
- Fácil de cambiar la lógica
- Menos propenso a errores de tipeo

---

### 4. Siempre usa HTTPS en producción

```php
// config/session.php
'secure' => env('SESSION_SECURE_COOKIE', true),

// .env (producción)
SESSION_SECURE_COOKIE=true
```

**¿Por qué?**
- Protege cookies de sesión
- Previene ataques man-in-the-middle
- Requerido para muchas APIs (ej: pagos)

---

### 5. Implementa rate limiting

```php
// routes/web.php
Route::middleware(['auth', 'throttle:60,1'])->group(function () {
    // Máximo 60 peticiones por minuto
});
```

**¿Por qué?**
- Previene ataques de fuerza bruta
- Protege contra DDoS
- Mejora el rendimiento

---

### 6. Registra acciones importantes

```php
public function destroy(Product $product)
{
    Log::info('Producto eliminado', [
        'product_id' => $product->id,
        'user_id' => auth()->id(),
        'user_email' => auth()->user()->email,
    ]);
    
    $product->delete();
    
    return redirect()->route('products.index')
        ->with('success', 'Producto eliminado correctamente.');
}
```

**¿Por qué?**
- Auditoría de seguridad
- Debugging
- Cumplimiento legal (GDPR, etc.)

---

### 7. Usa políticas (Policies) para lógica compleja

Si la lógica de autorización se vuelve compleja, usa Policies:

```bash
php artisan make:policy ProductPolicy --model=Product
```

```php
// app/Policies/ProductPolicy.php
public function delete(User $user, Product $product)
{
    return $user->isAdmin();
}

// Controlador
public function destroy(Product $product)
{
    $this->authorize('delete', $product);
    
    $product->delete();
    return redirect()->route('products.index')
        ->with('success', 'Producto eliminado correctamente.');
}
```

---

## 📚 Comandos Útiles

### Ejecutar migraciones
```bash
php artisan migrate
```

### Ejecutar migraciones y seeders (borra todo)
```bash
php artisan migrate:fresh --seed
```

### Crear un usuario manualmente
```bash
php artisan tinker
```
```php
User::create([
    'name' => 'Nuevo Admin',
    'email' => 'nuevo@example.com',
    'password' => Hash::make('password'),
    'role' => 'admin',
]);
```

### Ver rutas
```bash
php artisan route:list
```

### Limpiar caché
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 🎯 Resumen para Estudiantes

### Lo que DEBES recordar:

1. **Autenticación ≠ Autorización**
   - Autenticación: ¿Quién eres?
   - Autorización: ¿Qué puedes hacer?

2. **Middleware = Filtro de seguridad**
   - Se ejecuta antes del controlador
   - Verifica permisos

3. **Seguridad en capas**
   - Protege rutas (middleware)
   - Protege vistas (ocultar botones)
   - Protege controladores (verificación adicional)

4. **Siempre usa**:
   - `@csrf` en formularios
   - `Hash::make()` para contraseñas
   - `{{ }}` para mostrar datos (no `{!! !!}`)

5. **Nunca confíes en el frontend**
   - Siempre valida en el backend
   - Ocultar un botón NO es seguridad

---

## 📖 Recursos Adicionales

- [Documentación oficial de Laravel - Authentication](https://laravel.com/docs/12.x/authentication)
- [Documentación oficial de Laravel - Authorization](https://laravel.com/docs/12.x/authorization)
- [Laravel Breeze](https://laravel.com/docs/12.x/starter-kits#laravel-breeze)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/) - Vulnerabilidades más comunes

---

## ✅ Checklist de Seguridad

Antes de desplegar a producción, verifica:

- [ ] Todas las rutas protegidas tienen middleware `auth`
- [ ] Rutas sensibles tienen middleware `role`
- [ ] Todos los formularios tienen `@csrf`
- [ ] Todas las contraseñas usan `Hash::make()`
- [ ] Todos los datos mostrados usan `{{ }}` (no `{!! !!}`)
- [ ] Validación en todos los Form Requests
- [ ] HTTPS habilitado en producción
- [ ] Cookies seguras habilitadas
- [ ] Rate limiting configurado
- [ ] Logs de acciones importantes
- [ ] Variables sensibles en `.env` (no en código)
- [ ] `.env` en `.gitignore`

---

**Fecha de creación**: 2026-02-01  
**Versión de Laravel**: 12.47.0  
**Versión de PHP**: 8.5.2  
**Autor**: Sistema de Autenticación y Autorización - Proyecto Educativo

---

## 💡 Nota Final

Este sistema es educativo y apropiado para proyectos pequeños a medianos. Para aplicaciones empresariales con requisitos complejos de permisos, considera usar paquetes especializados como:

- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)
- [Laravel Sanctum](https://laravel.com/docs/12.x/sanctum) (para APIs)
- [Laravel Jetstream](https://jetstream.laravel.com/) (incluye equipos y roles)

**¡La seguridad es un proceso continuo, no un destino!** 🔒
