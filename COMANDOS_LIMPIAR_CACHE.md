# Comandos para Limpiar Caché y Solucionar Problemas

## 🔧 Ejecuta estos comandos en orden:

```bash
# 1. Limpiar caché de configuración
php artisan config:clear

# 2. Limpiar caché de rutas
php artisan route:clear

# 3. Limpiar caché de vistas
php artisan view:clear

# 4. Limpiar caché general
php artisan cache:clear

# 5. Verificar que las rutas están registradas
php artisan route:list --name=api-examples
```

## ✅ Deberías ver estas rutas:

```
GET|HEAD  api-examples ..................... api-examples.index › ApiExampleController@index
GET|HEAD  api-examples/get-users .......... api-examples.get-users › ApiExampleController@getUsers
```

## 🧪 Probar la configuración:

```bash
php artisan tinker
```

Luego dentro de tinker:

```php
// Verificar URL de la API
config('services.jsonplaceholder.url')
// Debería mostrar: "https://jsonplaceholder.typicode.com"

// Probar la API directamente
$response = \Illuminate\Support\Facades\Http::get('https://jsonplaceholder.typicode.com/users');
$response->successful()
// Debería mostrar: true

$users = $response->json();
count($users)
// Debería mostrar: 10

exit
```

## 🚀 Iniciar el servidor:

```bash
php artisan serve
```

Luego visita:
- http://localhost:8000/api-examples (menú principal)
- http://localhost:8000/api-examples/get-users (ejemplo de usuarios)

## ❌ Si aún no funciona:

### Verificar que estás autenticado:
Las rutas requieren login. Asegúrate de:
1. Tener un usuario creado
2. Estar logueado en la aplicación
3. Visitar las rutas después de login

### Verificar logs:
```bash
tail -f storage/logs/laravel.log
```

### Verificar permisos:
```bash
chmod -R 775 storage bootstrap/cache
```

## 🐛 Debug en el controlador:

Si quieres ver qué está pasando, descomenta la línea 45 en `ApiExampleController.php`:

```php
// Línea 45: Cambiar de:
//return $users;

// A:
return $users;
```

Esto mostrará el JSON crudo de la API para verificar que la petición funciona.
