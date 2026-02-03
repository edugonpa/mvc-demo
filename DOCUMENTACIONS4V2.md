# Documentación API RESTful Mejorada - Semana 4 V2

## 📚 Introducción

Esta documentación explica las mejoras implementadas en la API RESTful de productos, incluyendo:
- Respuestas JSON homogéneas
- Manejo de errores con `abort()` y `try/catch`
- API Resources para formatear datos
- Configuración de testing con SQLite en memoria

---

## 🎯 Mejoras Implementadas

### 1. Respuestas JSON Homogéneas
### 2. Manejo de Errores
### 3. ProductoResource
### 4. Testing con SQLite

---

## 📦 Archivos Creados/Modificados

```
mvc-demo/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       └── ProductoController.php      ← MODIFICADO
│   │   └── Resources/
│   │       └── ProductoResource.php            ← NUEVO
│   └── Models/
│       └── Product.php                         (sin cambios)
├── database/
│   └── factories/
│       └── ProductFactory.php                  ← NUEVO
├── tests/
│   └── Feature/
│       └── Api/
│           └── ProductoApiTest.php             ← NUEVO
├── .env.testing                                ← NUEVO
└── phpunit.xml                                 (ya configurado)
```

---

## 1️⃣ Respuestas JSON Homogéneas

### ¿Por qué es importante?

Las APIs profesionales deben tener un formato de respuesta consistente para que los clientes (frontend, mobile) sepan qué esperar siempre.

### Formato Estándar Implementado

```json
{
    "success": true,
    "message": "Descripción de la operación",
    "data": { ... }
}
```

### Estructura de Respuestas

#### ✅ Respuesta Exitosa

```json
{
    "success": true,
    "message": "Productos obtenidos exitosamente",
    "data": [
        {
            "id": 1,
            "nombre": "LAPTOP",
            "precio": "999.99",
            "category_id": null,
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z"
        }
    ]
}
```

#### ❌ Respuesta con Error de Validación (422)

```json
{
    "success": false,
    "message": "Error de validación",
    "errors": {
        "nombre": ["The nombre field is required."],
        "precio": ["The precio field is required."]
    }
}
```

#### ❌ Respuesta con Error del Servidor (500)

```json
{
    "success": false,
    "message": "Error al obtener productos",
    "error": "Mensaje técnico del error"
}
```

### Implementación en el Controlador

```php
public function index(): JsonResponse
{
    try {
        $productos = Product::all();
        
        return response()->json([
            'success' => true,
            'message' => 'Productos obtenidos exitosamente',
            'data' => ProductoResource::collection($productos)
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener productos',
            'error' => $e->getMessage()
        ], 500);
    }
}
```

### Ventajas

✅ **Consistencia**: Todas las respuestas tienen la misma estructura  
✅ **Claridad**: El cliente sabe si la operación fue exitosa con `success`  
✅ **Información**: Siempre hay un mensaje descriptivo  
✅ **Debugging**: Los errores incluyen información técnica  

---

## 2️⃣ Manejo de Errores

### Tipos de Errores Manejados

| Tipo | Código | Cuándo ocurre |
|------|--------|---------------|
| Validación | 422 | Datos inválidos |
| No encontrado | 404 | Recurso inexistente |
| Servidor | 500 | Error inesperado |

### Uso de `abort()`

```php
public function show(Product $producto): JsonResponse
{
    try {
        if (!$producto->exists) {
            abort(404, 'Producto no encontrado');
        }

        return response()->json([
            'success' => true,
            'message' => 'Producto obtenido exitosamente',
            'data' => new ProductoResource($producto)
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener producto',
            'error' => $e->getMessage()
        ], 500);
    }
}
```

### ¿Qué hace `abort()`?

- **Detiene la ejecución** inmediatamente
- **Lanza una excepción HTTP** con el código especificado
- **Permite mensaje personalizado**

### Uso de `try/catch`

```php
public function store(Request $request): JsonResponse
{
    try {
        // Validación
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0'
        ]);

        // Creación
        $producto = Product::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Producto creado exitosamente',
            'data' => new ProductoResource($producto)
        ], 201);
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Error de validación específico
        return response()->json([
            'success' => false,
            'message' => 'Error de validación',
            'errors' => $e->errors()
        ], 422);
        
    } catch (\Exception $e) {
        // Cualquier otro error
        return response()->json([
            'success' => false,
            'message' => 'Error al crear producto',
            'error' => $e->getMessage()
        ], 500);
    }
}
```

### Flujo de Manejo de Errores

```
Petición → try {
    Validación → ValidationException → catch → 422
    Lógica de negocio → Exception → catch → 500
    abort(404) → Exception → catch → 500
} → Respuesta JSON
```

---

## 3️⃣ ProductoResource

### ¿Qué es un API Resource?

Un **Resource** es una capa de transformación entre el modelo y la respuesta JSON. Permite:
- Formatear datos
- Ocultar campos sensibles
- Agregar campos calculados
- Mantener consistencia

### Archivo: `app/Http/Resources/ProductoResource.php`

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => strtoupper($this->nombre),
            'precio' => number_format($this->precio, 2, '.', ''),
            'category_id' => $this->category_id,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
```

### Transformaciones Aplicadas

| Campo | Transformación | Ejemplo |
|-------|----------------|---------|
| `nombre` | `strtoupper()` | "laptop" → "LAPTOP" |
| `precio` | `number_format()` | 999.9 → "999.90" |
| `created_at` | `toISOString()` | Formato ISO 8601 |
| `updated_at` | `toISOString()` | Formato ISO 8601 |

### Uso en el Controlador

#### Para un solo producto:

```php
return response()->json([
    'success' => true,
    'message' => 'Producto obtenido exitosamente',
    'data' => new ProductoResource($producto)
], 200);
```

#### Para una colección:

```php
return response()->json([
    'success' => true,
    'message' => 'Productos obtenidos exitosamente',
    'data' => ProductoResource::collection($productos)
], 200);
```

### Comparación: Antes vs Después

#### Antes (sin Resource):

```json
{
    "id": 1,
    "nombre": "laptop",
    "precio": 999.9,
    "created_at": "2024-01-15 10:30:00",
    "updated_at": "2024-01-15 10:30:00"
}
```

#### Después (con Resource):

```json
{
    "id": 1,
    "nombre": "LAPTOP",
    "precio": "999.90",
    "created_at": "2024-01-15T10:30:00.000000Z",
    "updated_at": "2024-01-15T10:30:00.000000Z"
}
```

### Ventajas de usar Resources

✅ **Separación de responsabilidades**: El modelo no se mezcla con la presentación  
✅ **Reutilización**: El mismo formato en todos los endpoints  
✅ **Mantenibilidad**: Cambios centralizados  
✅ **Flexibilidad**: Diferentes formatos para diferentes contextos  

---

## 4️⃣ Configuración de Testing

### Archivo: `.env.testing`

```env
APP_ENV=testing
APP_KEY=base64:test1234567890123456789012345678901234567890=
APP_DEBUG=true

# Base de datos en memoria
DB_CONNECTION=sqlite
DB_DATABASE=:memory:

# Drivers ligeros para testing
SESSION_DRIVER=array
CACHE_STORE=array
QUEUE_CONNECTION=sync
MAIL_MAILER=array
```

### ¿Por qué SQLite en memoria?

| Ventaja | Descripción |
|---------|-------------|
| **Velocidad** | No escribe en disco, todo en RAM |
| **Aislamiento** | Cada test tiene su propia BD |
| **Limpieza** | Se destruye al terminar |
| **Sin configuración** | No requiere servidor de BD |

### Archivo: `phpunit.xml`

Ya está configurado con:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

### Factory: `database/factories/ProductFactory.php`

```php
<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->words(3, true),
            'precio' => fake()->randomFloat(2, 10, 1000),
            'category_id' => null,
        ];
    }
}
```

### ¿Qué hace el Factory?

Genera datos falsos para testing:

```php
// Crear 1 producto
Product::factory()->create();

// Crear 5 productos
Product::factory()->count(5)->create();

// Crear con datos específicos
Product::factory()->create([
    'nombre' => 'laptop',
    'precio' => 999.99
]);
```

---

## 🧪 Tests Implementados

### Archivo: `tests/Feature/Api/ProductoApiTest.php`

### Tests Incluidos

| Test | Descripción |
|------|-------------|
| `test_puede_listar_todos_los_productos` | Verifica GET /api/productos |
| `test_puede_crear_un_producto` | Verifica POST /api/productos |
| `test_validacion_al_crear_producto_sin_datos` | Error 422 sin datos |
| `test_validacion_precio_negativo` | Validación de precio mínimo |
| `test_puede_mostrar_un_producto` | Verifica GET /api/productos/{id} |
| `test_error_404_producto_inexistente` | Error 404 |
| `test_puede_actualizar_un_producto` | Verifica PUT /api/productos/{id} |
| `test_puede_eliminar_un_producto` | Verifica DELETE /api/productos/{id} |
| `test_formato_respuesta_homogeneo` | Estructura consistente |
| `test_producto_resource_formatea_correctamente` | Transformaciones |

### Ejemplo de Test Completo

```php
public function test_puede_crear_un_producto(): void
{
    $data = [
        'nombre' => 'laptop',
        'precio' => 999.99
    ];

    $response = $this->postJson('/api/productos', $data);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'nombre',
                'precio',
                'category_id',
                'created_at',
                'updated_at'
            ]
        ])
        ->assertJson([
            'success' => true,
            'message' => 'Producto creado exitosamente',
            'data' => [
                'nombre' => 'LAPTOP', // Debe estar en mayúsculas
                'precio' => '999.99'  // Debe tener 2 decimales
            ]
        ]);

    // Verificar que existe en la base de datos
    $this->assertDatabaseHas('products', [
        'nombre' => 'laptop',
        'precio' => 999.99
    ]);
}
```

### Ejecutar Tests

```bash
# Todos los tests
php artisan test

# Solo tests de API
php artisan test --filter ProductoApiTest

# Con cobertura
php artisan test --coverage

# Test específico
php artisan test --filter test_puede_crear_un_producto
```

### Trait: `RefreshDatabase`

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductoApiTest extends TestCase
{
    use RefreshDatabase;
    
    // ...
}
```

**¿Qué hace?**
- Ejecuta migraciones antes de cada test
- Limpia la base de datos después de cada test
- Garantiza aislamiento entre tests

---

## 📊 Comparación: Antes vs Después

### Controlador

#### Antes:

```php
public function index()
{
    return response()->json(Product::all());
}
```

**Problemas:**
- ❌ Sin formato consistente
- ❌ Sin manejo de errores
- ❌ Sin mensaje descriptivo
- ❌ Datos sin formatear

#### Después:

```php
public function index(): JsonResponse
{
    try {
        $productos = Product::all();
        
        return response()->json([
            'success' => true,
            'message' => 'Productos obtenidos exitosamente',
            'data' => ProductoResource::collection($productos)
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener productos',
            'error' => $e->getMessage()
        ], 500);
    }
}
```

**Mejoras:**
- ✅ Formato homogéneo
- ✅ Manejo de errores
- ✅ Mensaje descriptivo
- ✅ Datos formateados con Resource
- ✅ Type hints (`JsonResponse`)

---

## 🔍 Conceptos Clave Explicados

### 1. API Resources

**Analogía:** Un traductor entre tu base de datos y el cliente.

```
Base de datos → Modelo → Resource → JSON → Cliente
```

### 2. Try/Catch

**Analogía:** Una red de seguridad para errores.

```php
try {
    // Intenta hacer algo
} catch (TipoDeError $e) {
    // Si falla, maneja el error
}
```

### 3. abort()

**Analogía:** Un botón de emergencia que detiene todo.

```php
abort(404, 'No encontrado'); // Detiene y devuelve 404
```

### 4. RefreshDatabase

**Analogía:** Resetear un videojuego antes de cada partida.

```php
use RefreshDatabase; // BD limpia en cada test
```

### 5. Factory

**Analogía:** Una fábrica que produce datos de prueba.

```php
Product::factory()->create(); // Crea producto falso
```

---

## 🎓 Ejercicios para Estudiantes

### Nivel Básico

1. **Ejecutar todos los tests** y verificar que pasen
2. **Crear un producto** con Postman y verificar el formato
3. **Provocar un error 422** enviando datos inválidos

### Nivel Intermedio

4. **Agregar un nuevo campo** al ProductoResource (ej: `nombre_corto`)
5. **Crear un test** para verificar actualización con datos inválidos
6. **Modificar el Resource** para ocultar `category_id` si es null

### Nivel Avanzado

7. **Implementar paginación** en `index()` con Resource
8. **Crear un Resource** para errores personalizados
9. **Agregar filtros** en `index()` y crear tests

---

## 📝 Endpoints de la API

### Resumen Completo

| Método | Endpoint | Acción | Respuesta |
|--------|----------|--------|-----------|
| GET | `/api/productos` | Listar todos | 200 + array |
| POST | `/api/productos` | Crear | 201 + objeto |
| GET | `/api/productos/{id}` | Ver uno | 200 + objeto |
| PUT/PATCH | `/api/productos/{id}` | Actualizar | 200 + objeto |
| DELETE | `/api/productos/{id}` | Eliminar | 200 + null |

### Ejemplos de Uso

#### Crear Producto

**Request:**
```bash
curl -X POST http://localhost:8000/api/productos \
  -H "Content-Type: application/json" \
  -d '{
    "nombre": "mouse gamer",
    "precio": 45.5
  }'
```

**Response:**
```json
{
    "success": true,
    "message": "Producto creado exitosamente",
    "data": {
        "id": 1,
        "nombre": "MOUSE GAMER",
        "precio": "45.50",
        "category_id": null,
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z"
    }
}
```

---

## ❓ Preguntas Frecuentes

### ¿Por qué usar Resources en vez de devolver el modelo directamente?

**Respuesta:** Los Resources permiten:
- Formatear datos (mayúsculas, decimales)
- Ocultar campos sensibles
- Agregar campos calculados
- Mantener consistencia

### ¿Cuándo usar `abort()` vs `throw new Exception()`?

**Respuesta:**
- `abort()`: Para errores HTTP conocidos (404, 403, etc.)
- `throw new Exception()`: Para errores de lógica de negocio

### ¿Por qué SQLite en memoria para testing?

**Respuesta:** Es más rápido y no afecta la base de datos de desarrollo.

### ¿Debo usar try/catch en todos los métodos?

**Respuesta:** Sí, para APIs es una buena práctica capturar todos los errores posibles.

---

## 🚀 Comandos Útiles

```bash
# Ejecutar tests
php artisan test

# Ver rutas API
php artisan route:list --path=api

# Crear un Resource
php artisan make:resource NombreResource

# Crear un Factory
php artisan make:factory NombreFactory

# Crear un Test
php artisan make:test Api/NombreTest

# Limpiar caché
php artisan cache:clear
php artisan config:clear
```

---

## 📚 Recursos Adicionales

- [Laravel API Resources](https://laravel.com/docs/11.x/eloquent-resources)
- [Laravel Testing](https://laravel.com/docs/11.x/testing)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [REST API Best Practices](https://restfulapi.net/)

---

## ✅ Checklist de Implementación

- [x] Respuestas JSON homogéneas con `success`, `message`, `data`
- [x] Manejo de errores con `try/catch`
- [x] Uso de `abort()` para errores HTTP
- [x] ProductoResource con transformaciones
- [x] `strtoupper()` para nombre
- [x] `number_format()` para precio
- [x] `Resource::collection()` para listados
- [x] `.env.testing` configurado
- [x] SQLite en memoria
- [x] ProductFactory creado
- [x] Tests completos implementados
- [x] RefreshDatabase en tests

---

## 🎯 Resumen Final

### Mejoras Implementadas

1. **Respuestas Homogéneas**
   - Estructura consistente: `success`, `message`, `data`
   - Fácil de consumir desde frontend

2. **Manejo de Errores**
   - `try/catch` en todos los métodos
   - `abort()` para errores HTTP
   - Mensajes descriptivos

3. **ProductoResource**
   - Nombre en mayúsculas
   - Precio con 2 decimales
   - Fechas en formato ISO

4. **Testing**
   - SQLite en memoria
   - 10 tests completos
   - Factory para datos de prueba

### Beneficios

✅ **Profesional**: API lista para producción  
✅ **Mantenible**: Código organizado y documentado  
✅ **Testeable**: Cobertura completa de tests  
✅ **Consistente**: Respuestas predecibles  
✅ **Robusto**: Manejo de errores completo  

---

## 🎓 Para el Instructor

### Puntos Clave a Explicar

1. **Por qué Resources**: Separación de responsabilidades
2. **Try/Catch**: Prevención de errores no manejados
3. **Testing**: Confianza en el código
4. **Formato homogéneo**: Experiencia del desarrollador

### Demostración Sugerida

1. Mostrar respuesta sin Resource vs con Resource
2. Provocar un error y mostrar el manejo
3. Ejecutar tests en vivo
4. Comparar código antes y después

---

**Documentación creada para Semana 4 - API RESTful Avanzada**
