# Documentación API RESTful - Productos

## 📚 Introducción

Esta es una API RESTful básica para gestionar productos. Está diseñada con fines educativos para entender los conceptos fundamentales de las APIs REST en Laravel.

---

## 🎯 Conceptos Clave

### ¿Qué es una API RESTful?

REST (Representational State Transfer) es un estilo arquitectónico que usa:
- **HTTP** como protocolo
- **JSON** como formato de datos
- **Verbos HTTP** para definir acciones (GET, POST, PUT, DELETE)
- **URLs** para identificar recursos

### Principios REST aplicados:

1. **Recursos**: Los productos son nuestro recurso (`/api/productos`)
2. **Verbos HTTP**: Cada verbo tiene un propósito específico
3. **Sin estado**: Cada petición es independiente
4. **Respuestas JSON**: Siempre devolvemos JSON, nunca HTML

---

## 🗂️ Estructura del Proyecto

```
mvc-demo/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Api/
│   │           └── ProductoController.php  ← Controlador API
│   └── Models/
│       └── Product.php                     ← Modelo Eloquent
└── routes/
    └── api.php                             ← Rutas API
```

---

## 🛣️ Rutas Disponibles

Laravel genera automáticamente 5 rutas con `Route::apiResource()`:

| Método HTTP | URI                      | Acción      | Descripción                    |
|-------------|--------------------------|-------------|--------------------------------|
| GET         | `/api/productos`         | index       | Listar todos los productos     |
| POST        | `/api/productos`         | store       | Crear un nuevo producto        |
| GET         | `/api/productos/{id}`    | show        | Ver un producto específico     |
| PUT/PATCH   | `/api/productos/{id}`    | update      | Actualizar un producto         |
| DELETE      | `/api/productos/{id}`    | destroy     | Eliminar un producto           |

### Ver las rutas en terminal:

```bash
php artisan route:list --path=api
```

---

## 📝 Archivo: routes/api.php

```php
<?php

use App\Http\Controllers\Api\ProductoController;
use Illuminate\Support\Facades\Route;

Route::apiResource('productos', ProductoController::class);
```

### Explicación:

- **`apiResource`**: Crea las 5 rutas REST automáticamente
- **`productos`**: Nombre del recurso (se convierte en `/api/productos`)
- **Prefijo `/api`**: Laravel lo agrega automáticamente a todas las rutas en `api.php`

---

## ⚙️ Configuración Importante (Laravel 11)

En Laravel 11, debes registrar `api.php` en `bootstrap/app.php`:

```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',  // ← Agregar esta línea
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
```

**Sin esta configuración, las rutas API no funcionarán.**

---

## 🎮 Controlador: ProductoController.php

### Estructura completa:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    // Método 1: Listar todos
    public function index()
    {
        return response()->json(Product::all());
    }

    // Método 2: Crear nuevo
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string',
            'precio' => 'required|numeric'
        ]);

        $producto = Product::create($data);

        return response()->json($producto, 201);
    }

    // Método 3: Ver uno específico
    public function show(Product $producto)
    {
        return response()->json($producto);
    }

    // Método 4: Actualizar
    public function update(Request $request, Product $producto)
    {
        $producto->update(
            $request->validate([
                'nombre' => 'required|string',
                'precio' => 'required|numeric'
            ])
        );

        return response()->json($producto, 200);
    }

    // Método 5: Eliminar
    public function destroy(Product $producto)
    {
        $producto->delete();

        return response()->json(null, 204);
    }
}
```

---

## 🔍 Explicación Método por Método

### 1️⃣ **index()** - Listar todos los productos

```php
public function index()
{
    return response()->json(Product::all());
}
```

**¿Qué hace?**
- `Product::all()`: Obtiene todos los productos de la base de datos
- `response()->json()`: Convierte los datos a formato JSON
- Código HTTP: **200 OK** (por defecto)

**Petición:**
```http
GET /api/productos
```

**Respuesta:**
```json
[
    {
        "id": 1,
        "nombre": "Laptop",
        "precio": "999.99",
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z"
    },
    {
        "id": 2,
        "nombre": "Mouse",
        "precio": "25.50",
        "created_at": "2024-01-15T11:00:00.000000Z",
        "updated_at": "2024-01-15T11:00:00.000000Z"
    }
]
```

---

### 2️⃣ **store()** - Crear un nuevo producto

```php
public function store(Request $request)
{
    $data = $request->validate([
        'nombre' => 'required|string',
        'precio' => 'required|numeric'
    ]);

    $producto = Product::create($data);

    return response()->json($producto, 201);
}
```

**¿Qué hace?**
1. **Validación**: Verifica que los datos sean correctos
   - `nombre`: Obligatorio y debe ser texto
   - `precio`: Obligatorio y debe ser numérico
2. **Creación**: Inserta el producto en la base de datos
3. **Respuesta**: Devuelve el producto creado con código **201 Created**

**Petición:**
```http
POST /api/productos
Content-Type: application/json

{
    "nombre": "Teclado",
    "precio": 45.99
}
```

**Respuesta exitosa (201):**
```json
{
    "id": 3,
    "nombre": "Teclado",
    "precio": "45.99",
    "created_at": "2024-01-15T12:00:00.000000Z",
    "updated_at": "2024-01-15T12:00:00.000000Z"
}
```

**Respuesta con error de validación (422):**
```json
{
    "message": "The nombre field is required. (and 1 more error)",
    "errors": {
        "nombre": ["The nombre field is required."],
        "precio": ["The precio field is required."]
    }
}
```

---

### 3️⃣ **show()** - Ver un producto específico

```php
public function show(Product $producto)
{
    return response()->json($producto);
}
```

**¿Qué hace?**
- **Route Model Binding**: Laravel busca automáticamente el producto por ID
- Si existe: devuelve el producto
- Si no existe: devuelve error **404 Not Found** automáticamente

**Petición:**
```http
GET /api/productos/1
```

**Respuesta exitosa (200):**
```json
{
    "id": 1,
    "nombre": "Laptop",
    "precio": "999.99",
    "created_at": "2024-01-15T10:30:00.000000Z",
    "updated_at": "2024-01-15T10:30:00.000000Z"
}
```

**Respuesta si no existe (404):**
```json
{
    "message": "No query results for model [App\\Models\\Product] 999"
}
```

---

### 4️⃣ **update()** - Actualizar un producto

```php
public function update(Request $request, Product $producto)
{
    $producto->update(
        $request->validate([
            'nombre' => 'required|string',
            'precio' => 'required|numeric'
        ])
    );

    return response()->json($producto, 200);
}
```

**¿Qué hace?**
1. **Route Model Binding**: Encuentra el producto automáticamente
2. **Validación**: Verifica los datos nuevos
3. **Actualización**: Modifica el producto en la base de datos
4. **Respuesta**: Devuelve el producto actualizado con código **200 OK**

**Petición:**
```http
PUT /api/productos/1
Content-Type: application/json

{
    "nombre": "Laptop Gaming",
    "precio": 1299.99
}
```

**Respuesta (200):**
```json
{
    "id": 1,
    "nombre": "Laptop Gaming",
    "precio": "1299.99",
    "created_at": "2024-01-15T10:30:00.000000Z",
    "updated_at": "2024-01-15T13:00:00.000000Z"
}
```

---

### 5️⃣ **destroy()** - Eliminar un producto

```php
public function destroy(Product $producto)
{
    $producto->delete();

    return response()->json(null, 204);
}
```

**¿Qué hace?**
1. **Route Model Binding**: Encuentra el producto
2. **Eliminación**: Borra el producto de la base de datos
3. **Respuesta**: Devuelve `null` con código **204 No Content**

**Petición:**
```http
DELETE /api/productos/1
```

**Respuesta (204):**
```
(Sin contenido - solo código de estado 204)
```

---

## 🔐 Validación de Datos

### Reglas usadas:

```php
$request->validate([
    'nombre' => 'required|string',
    'precio' => 'required|numeric'
]);
```

| Regla      | Significado                                    |
|------------|------------------------------------------------|
| `required` | El campo es obligatorio                        |
| `string`   | Debe ser texto                                 |
| `numeric`  | Debe ser un número (entero o decimal)         |

### ¿Qué pasa si la validación falla?

Laravel automáticamente:
1. Detiene la ejecución del método
2. Devuelve un error **422 Unprocessable Entity**
3. Incluye los mensajes de error en formato JSON

---

## 📊 Códigos de Estado HTTP

| Código | Nombre                  | Cuándo se usa                          |
|--------|-------------------------|----------------------------------------|
| 200    | OK                      | Operación exitosa (GET, PUT)           |
| 201    | Created                 | Recurso creado exitosamente (POST)     |
| 204    | No Content              | Eliminación exitosa (DELETE)           |
| 404    | Not Found               | Recurso no encontrado                  |
| 422    | Unprocessable Entity    | Error de validación                    |

---

## 🧪 Probar la API

### Opción 1: Usando cURL

**Listar todos:**
```bash
curl http://localhost:8000/api/productos
```

**Crear producto:**
```bash
curl -X POST http://localhost:8000/api/productos \
  -H "Content-Type: application/json" \
  -d '{"nombre":"Monitor","precio":299.99}'
```

**Ver producto:**
```bash
curl http://localhost:8000/api/productos/1
```

**Actualizar producto:**
```bash
curl -X PUT http://localhost:8000/api/productos/1 \
  -H "Content-Type: application/json" \
  -d '{"nombre":"Monitor 4K","precio":399.99}'
```

**Eliminar producto:**
```bash
curl -X DELETE http://localhost:8000/api/productos/1
```

---

### Opción 2: Usando Postman

1. **Crear nueva colección** llamada "API Productos"
2. **Agregar requests** para cada endpoint:
   - GET `/api/productos`
   - POST `/api/productos` (con Body → raw → JSON)
   - GET `/api/productos/1`
   - PUT `/api/productos/1` (con Body → raw → JSON)
   - DELETE `/api/productos/1`

---

### Opción 3: Usando Thunder Client (VS Code)

1. Instalar extensión "Thunder Client"
2. Crear nueva request
3. Configurar método, URL y body (si aplica)
4. Enviar petición

---

## 🔑 Conceptos Importantes

### 1. Route Model Binding

```php
public function show(Product $producto)
```

Laravel automáticamente:
- Busca el producto por el ID en la URL
- Si existe: lo inyecta en el método
- Si no existe: devuelve 404

**Sin Route Model Binding sería:**
```php
public function show($id)
{
    $producto = Product::findOrFail($id);
    return response()->json($producto);
}
```

---

### 2. Eloquent ORM

```php
Product::all()           // SELECT * FROM products
Product::create($data)   // INSERT INTO products
$producto->update($data) // UPDATE products WHERE id = ?
$producto->delete()      // DELETE FROM products WHERE id = ?
```

No escribimos SQL directamente, Eloquent lo hace por nosotros.

---

### 3. JSON Responses

```php
return response()->json($data, $statusCode);
```

Convierte automáticamente:
- Arrays → JSON
- Objetos Eloquent → JSON
- Colecciones → JSON

---

### 4. Validación Inline

```php
$data = $request->validate([...]);
```

**Ventajas:**
- Simple y directo
- Automáticamente devuelve errores 422
- Perfecto para APIs pequeñas

**Para proyectos grandes:** Usar Form Requests

---

## 🎓 Ejercicios para Estudiantes

### Nivel Básico:

1. **Probar todos los endpoints** con Postman o cURL
2. **Intentar crear un producto sin nombre** (ver error de validación)
3. **Buscar un producto que no existe** (ver error 404)

### Nivel Intermedio:

4. **Agregar validación para `category_id`** (opcional)
5. **Modificar `index()` para paginar** resultados:
   ```php
   return response()->json(Product::paginate(10));
   ```
6. **Agregar filtros** en `index()`:
   ```php
   $productos = Product::when($request->nombre, function($query, $nombre) {
       return $query->where('nombre', 'like', "%{$nombre}%");
   })->get();
   ```

### Nivel Avanzado:

7. **Crear API Resource** para formatear respuestas
8. **Agregar autenticación** con Sanctum
9. **Implementar búsqueda y ordenamiento** avanzado

---

## ❓ Preguntas Frecuentes

### ¿Por qué usar `apiResource` en vez de `resource`?

`apiResource` omite las rutas `create` y `edit` porque las APIs no necesitan formularios HTML.

### ¿Puedo usar PATCH en vez de PUT?

Sí, Laravel acepta ambos para el método `update()`.

### ¿Cómo pruebo la API sin frontend?

Usa Postman, Thunder Client, cURL o Insomnia.

### ¿Necesito autenticación?

Para este ejemplo básico no, pero en producción sí (usa Laravel Sanctum).

---

## 📚 Recursos Adicionales

- [Documentación oficial de Laravel - API Resources](https://laravel.com/docs/11.x/eloquent-resources)
- [Documentación oficial de Laravel - Validation](https://laravel.com/docs/11.x/validation)
- [REST API Tutorial](https://restfulapi.net/)

---

## ✅ Checklist de Implementación

- [x] Crear controlador `Api/ProductoController`
- [x] Definir rutas en `routes/api.php`
- [x] **Registrar `api.php` en `bootstrap/app.php` (Laravel 11)**
- [x] Usar modelo `Product` existente
- [x] Implementar validación en `store` y `update`
- [x] Responder solo JSON (sin vistas)
- [x] Usar códigos HTTP correctos
- [x] Implementar Route Model Binding

---

## 🎯 Resumen Final

Esta API RESTful implementa las operaciones CRUD básicas:

- **C**reate → `POST /api/productos`
- **R**ead → `GET /api/productos` y `GET /api/productos/{id}`
- **U**pdate → `PUT /api/productos/{id}`
- **D**elete → `DELETE /api/productos/{id}`

Todo con:
- ✅ Validación automática
- ✅ Respuestas JSON
- ✅ Códigos HTTP correctos
- ✅ Route Model Binding
- ✅ Eloquent ORM
- ✅ Sin librerías externas
- ✅ Código simple y educativo
