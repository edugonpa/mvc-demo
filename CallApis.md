# Guía Pedagógica: Consumo de APIs Externas con Laravel HTTP Client

## 📚 Índice
1. [Introducción](#introducción)
2. [Configuración Inicial](#configuración-inicial)
3. [Ejemplos Implementados](#ejemplos-implementados)
4. [Cómo Explicar a Estudiantes](#cómo-explicar-a-estudiantes)
5. [Ejercicios Prácticos](#ejercicios-prácticos)
6. [Troubleshooting](#troubleshooting)

---

## Introducción

### ¿Qué es Laravel HTTP Client?

Laravel HTTP Client es una interfaz expresiva y mínima construida sobre **Guzzle HTTP Client** que permite realizar peticiones HTTP de manera sencilla. Es la forma nativa y recomendada de consumir APIs externas en Laravel.

### ¿Por qué usar HTTP Client en lugar de cURL o Guzzle directamente?

- **Sintaxis más limpia y expresiva**
- **Integración nativa con Laravel**
- **Manejo automático de JSON**
- **Testing más sencillo**
- **Menos código boilerplate**

---

## Configuración Inicial

### Paso 1: Variables de Entorno

Se agregaron las siguientes variables al archivo `.env`:

```env
# API Externa - Configuración para ejemplos
JSONPLACEHOLDER_API_URL=https://jsonplaceholder.typicode.com
EXTERNAL_API_URL=https://api.example.com
EXTERNAL_API_TOKEN=your_api_token_here

# Configuración de timeouts y retries
API_TIMEOUT=30
API_RETRY_TIMES=3
API_RETRY_SLEEP=100
```

**Explicación para estudiantes:**
- Las variables de entorno permiten cambiar configuraciones sin modificar código
- Nunca se deben commitear tokens o credenciales reales
- Usar `.env.example` como plantilla sin valores sensibles

### Paso 2: Configuración en `config/services.php`

Se agregó la configuración de servicios externos:

```php
'jsonplaceholder' => [
    'url' => env('JSONPLACEHOLDER_API_URL', 'https://jsonplaceholder.typicode.com'),
],

'external_api' => [
    'url' => env('EXTERNAL_API_URL', 'https://api.example.com'),
    'token' => env('EXTERNAL_API_TOKEN', ''),
],

'api' => [
    'timeout' => env('API_TIMEOUT', 30),
    'retry_times' => env('API_RETRY_TIMES', 3),
    'retry_sleep' => env('API_RETRY_SLEEP', 100),
],
```

**Explicación para estudiantes:**
- `config/services.php` centraliza la configuración de servicios externos
- Usar `config()` helper en lugar de `env()` directamente en el código
- Los valores por defecto aseguran que la app funcione aunque falten variables

### Paso 3: Controlador `ApiExampleController`

Se creó un controlador con 8 ejemplos prácticos:

```bash
app/Http/Controllers/ApiExampleController.php
```

### Paso 4: Rutas

Se agregaron rutas protegidas con autenticación en `routes/web.php`:

```php
Route::prefix('api-examples')->middleware(['auth'])->group(function () {
    Route::get('/', [ApiExampleController::class, 'index'])->name('api-examples.index');
    Route::get('/get-users', [ApiExampleController::class, 'getUsers'])->name('api-examples.get-users');
    // ... más rutas
});
```

**Explicación para estudiantes:**
- Las rutas están protegidas: solo usuarios autenticados pueden acceder
- El prefijo `api-examples` agrupa todas las rutas relacionadas
- Los nombres de ruta facilitan la generación de URLs

### Paso 5: Vistas

Se crearon vistas Blade en `resources/views/api-examples/`:
- `index.blade.php` - Menú principal
- `get-users.blade.php` - Ejemplo GET simple
- `get-posts.blade.php` - Ejemplo GET con parámetros
- `create-post.blade.php` - Ejemplo POST
- `timeout-retry.blade.php` - Ejemplo timeout/retry
- `authentication.blade.php` - Ejemplo autenticación
- `error-handling.blade.php` - Ejemplo manejo de errores
- `custom-headers.blade.php` - Ejemplo headers personalizados
- `concurrent.blade.php` - Ejemplo requests concurrentes

---

## Ejemplos Implementados

### Ejemplo 1: GET Request Simple

**Objetivo:** Obtener una lista de usuarios desde una API pública.

**Código:**
```php
$response = Http::get(config('services.jsonplaceholder.url') . '/users');

if ($response->successful()) {
    $users = $response->json();
    // Procesar usuarios
}
```

**Conceptos clave:**
- `Http::get()` - Realiza petición GET
- `successful()` - Verifica si el status es 2xx (200-299)
- `json()` - Convierte respuesta JSON a array PHP
- `status()` - Obtiene código de estado HTTP

**Cómo explicarlo:**
1. Mostrar la URL de la API en el navegador
2. Explicar que `Http::get()` hace lo mismo que abrir esa URL
3. Demostrar que `json()` convierte el texto JSON en array PHP
4. Mostrar cómo iterar sobre los datos en la vista

---

### Ejemplo 2: GET con Query Parameters

**Objetivo:** Filtrar datos usando parámetros de consulta.

**Código:**
```php
$response = Http::get(config('services.jsonplaceholder.url') . '/posts', [
    'userId' => $userId
]);
```

**Conceptos clave:**
- Query parameters se pasan como segundo argumento (array)
- Laravel construye automáticamente la URL: `?userId=1`
- Útil para filtrado, paginación, búsquedas

**Cómo explicarlo:**
1. Mostrar cómo se ve la URL final: `/posts?userId=1`
2. Comparar con hacer la búsqueda manualmente en el navegador
3. Explicar que el array se convierte en query string automáticamente
4. Demostrar con diferentes valores de userId

---

### Ejemplo 3: POST Request

**Objetivo:** Crear un recurso enviando datos a la API.

**Código:**
```php
$response = Http::post(config('services.jsonplaceholder.url') . '/posts', [
    'title' => $validated['title'],
    'body' => $validated['body'],
    'userId' => $validated['userId']
]);
```

**Conceptos clave:**
- `Http::post()` - Realiza petición POST
- Los datos se envían automáticamente como JSON
- Siempre validar datos antes de enviar
- JSONPlaceholder simula la creación (no guarda realmente)

**Cómo explicarlo:**
1. Explicar la diferencia entre GET (obtener) y POST (crear)
2. Mostrar que los datos van en el "cuerpo" de la petición, no en la URL
3. Demostrar la validación de datos con `$request->validate()`
4. Explicar que la API devuelve el recurso creado con un ID

---

### Ejemplo 4: Timeout y Retry

**Objetivo:** Configurar timeouts y reintentos automáticos.

**Código:**
```php
$response = Http::timeout(30)
    ->retry(3, 100)
    ->get(config('services.jsonplaceholder.url') . '/posts/1');
```

**Conceptos clave:**
- `timeout()` - Tiempo máximo de espera en segundos
- `retry(times, sleep)` - Reintentos automáticos si falla
- `sleep` - Milisegundos de espera entre reintentos
- Previene que la aplicación se quede colgada

**Cómo explicarlo:**
1. Explicar qué es un timeout (tiempo de espera máximo)
2. Demostrar qué pasa si una API es lenta o no responde
3. Explicar que retry intenta de nuevo automáticamente
4. Mostrar la configuración en `.env` para cambiar valores

**Analogía útil:**
"Es como llamar por teléfono: si no contestan en 30 segundos (timeout), cuelgas. Y si está ocupado, intentas 3 veces más (retry) esperando 100ms entre cada intento."

---

### Ejemplo 5: Autenticación Bearer Token

**Objetivo:** Enviar requests con autenticación.

**Código:**
```php
$token = config('services.external_api.token');

$response = Http::withToken($token)
    ->get(config('services.jsonplaceholder.url') . '/posts/1');
```

**Conceptos clave:**
- `withToken()` - Agrega header: `Authorization: Bearer {token}`
- Método más común de autenticación en APIs REST
- El token se obtiene típicamente de un endpoint de login
- Nunca hardcodear tokens en el código

**Cómo explicarlo:**
1. Explicar qué es un token (como una llave digital)
2. Mostrar que el token va en los headers HTTP
3. Demostrar cómo obtener un token (login en otra API)
4. Explicar la importancia de la seguridad (HTTPS, .env)

**Otros métodos de autenticación:**
```php
// Basic Auth
Http::withBasicAuth('username', 'password')

// Digest Auth
Http::withDigestAuth('username', 'password')

// API Key en headers
Http::withHeaders(['X-API-Key' => 'key'])
```

---

### Ejemplo 6: Manejo de Errores

**Objetivo:** Diferentes formas de manejar errores HTTP.

**Código:**
```php
// Método 1: Verificación manual
if ($response->successful()) {
    // Procesar datos
} else {
    // Manejar error
}

// Método 2: Con excepciones
try {
    $response = Http::get($url)->throw();
    $data = $response->json();
} catch (Exception $e) {
    Log::error($e->getMessage());
}

// Método 3: Verificación específica
if ($response->unauthorized()) {
    // Renovar token
} elseif ($response->serverError()) {
    // Reintentar más tarde
}
```

**Métodos de verificación disponibles:**
- `successful()` - 200-299
- `ok()` - 200
- `created()` - 201
- `accepted()` - 202
- `noContent()` - 204
- `failed()` - 400-599
- `clientError()` - 400-499
- `serverError()` - 500-599
- `unauthorized()` - 401
- `forbidden()` - 403

**Cómo explicarlo:**
1. Explicar los códigos de estado HTTP (200, 404, 500, etc.)
2. Mostrar que no todas las peticiones son exitosas
3. Demostrar diferentes estrategias según el tipo de error
4. Explicar la importancia de loguear errores

**Analogía útil:**
"Los códigos HTTP son como semáforos: 2xx (verde) = todo bien, 4xx (amarillo) = error tuyo, 5xx (rojo) = error del servidor."

---

### Ejemplo 7: Headers Personalizados

**Objetivo:** Enviar headers HTTP personalizados.

**Código:**
```php
$response = Http::withHeaders([
    'X-Custom-Header' => 'Laravel-Example',
    'Accept' => 'application/json',
    'User-Agent' => 'Laravel-HTTP-Client/1.0'
])->get($url);
```

**Headers comunes:**
- `Accept` - Formato de respuesta deseado
- `Content-Type` - Tipo de contenido enviado
- `User-Agent` - Identifica la aplicación
- `Authorization` - Credenciales de autenticación

**Casos de uso:**
```php
// API Versioning
Http::withHeaders(['Accept' => 'application/vnd.api.v2+json'])

// Custom API Keys
Http::withHeaders(['X-API-Key' => config('services.api.key')])

// Request ID Tracking
Http::withHeaders(['X-Request-ID' => Str::uuid()])
```

**Cómo explicarlo:**
1. Explicar qué son los headers HTTP (metadatos de la petición)
2. Mostrar headers comunes y su propósito
3. Demostrar cómo algunas APIs requieren headers específicos
4. Explicar métodos alternativos: `accept()`, `contentType()`

---

### Ejemplo 8: Requests Concurrentes

**Objetivo:** Realizar múltiples requests en paralelo.

**Código:**
```php
$responses = Http::pool(fn ($pool) => [
    $pool->get($url . '/posts/1'),
    $pool->get($url . '/posts/2'),
    $pool->get($url . '/posts/3'),
]);

foreach ($responses as $response) {
    if ($response->successful()) {
        $data = $response->json();
    }
}
```

**Conceptos clave:**
- `Http::pool()` - Ejecuta múltiples requests en paralelo
- Más rápido que requests secuenciales
- Cada response se maneja independientemente
- Reduce tiempo total de espera significativamente

**Comparación de rendimiento:**
```
Secuencial:
Request 1: 1s
Request 2: 1s
Request 3: 1s
Total: 3s

Concurrente:
Request 1: 1s
Request 2: 1s (paralelo)
Request 3: 1s (paralelo)
Total: ~1s
```

**Cómo explicarlo:**
1. Explicar la diferencia entre secuencial y paralelo
2. Usar analogía: "Como hacer varias llamadas telefónicas al mismo tiempo"
3. Demostrar el ahorro de tiempo con ejemplos reales
4. Advertir sobre límites de rate limiting

**Consideraciones:**
- No abusar: demasiados requests pueden sobrecargar
- Verificar límites de rate limiting de la API
- Manejar errores individualmente
- Limitar a 5-10 requests concurrentes máximo

---

## Cómo Explicar a Estudiantes

### Sesión 1: Introducción (30 minutos)

**1. Conceptos básicos (10 min)**
- ¿Qué es una API?
- ¿Qué es REST?
- ¿Qué son los métodos HTTP? (GET, POST, PUT, DELETE)
- ¿Qué son los códigos de estado?

**2. Demostración en vivo (10 min)**
- Abrir JSONPlaceholder en el navegador
- Mostrar respuestas JSON
- Explicar estructura de datos
- Usar herramientas de desarrollador del navegador

**3. Primer ejemplo en Laravel (10 min)**
- Mostrar Ejemplo 1: GET Request Simple
- Ejecutar y ver resultados
- Explicar el código línea por línea
- Mostrar cómo se procesan los datos en la vista

### Sesión 2: GET y POST (45 minutos)

**1. GET con parámetros (15 min)**
- Ejemplo 2: GET con Query Parameters
- Explicar query strings
- Demostrar filtrado de datos
- Ejercicio: cambiar parámetros

**2. POST Request (15 min)**
- Ejemplo 3: POST Request
- Explicar diferencia GET vs POST
- Demostrar validación de datos
- Ejercicio: crear diferentes posts

**3. Práctica guiada (15 min)**
- Estudiantes crean su propio endpoint
- Modifican el formulario
- Agregan validaciones adicionales

### Sesión 3: Configuración Avanzada (45 minutos)

**1. Timeout y Retry (15 min)**
- Ejemplo 4: Timeout y Retry
- Explicar por qué son importantes
- Demostrar configuración en .env
- Ejercicio: cambiar valores y observar

**2. Autenticación (15 min)**
- Ejemplo 5: Autenticación Bearer Token
- Explicar tokens y seguridad
- Mostrar diferentes métodos de auth
- Ejercicio: configurar token propio

**3. Manejo de errores (15 min)**
- Ejemplo 6: Manejo de Errores
- Explicar códigos de estado
- Demostrar diferentes estrategias
- Ejercicio: manejar error 404

### Sesión 4: Temas Avanzados (45 minutos)

**1. Headers personalizados (15 min)**
- Ejemplo 7: Headers Personalizados
- Explicar qué son los headers
- Mostrar casos de uso reales
- Ejercicio: agregar headers propios

**2. Requests concurrentes (15 min)**
- Ejemplo 8: Requests Concurrentes
- Explicar ventajas de paralelización
- Demostrar diferencia de rendimiento
- Ejercicio: obtener múltiples recursos

**3. Proyecto integrador (15 min)**
- Combinar todos los conceptos
- Crear mini-aplicación que consuma API
- Presentación de trabajos

---

## Ejercicios Prácticos

### Ejercicio 1: Básico - Lista de Tareas
**Objetivo:** Consumir API de JSONPlaceholder para mostrar todos (tareas).

**Pasos:**
1. Crear método `getTodos()` en el controlador
2. Hacer GET a `/todos`
3. Mostrar en una tabla con checkbox para completadas
4. Agregar filtro por usuario

**Solución:**
```php
public function getTodos(Request $request)
{
    $userId = $request->input('userId');
    
    $response = Http::get(config('services.jsonplaceholder.url') . '/todos', [
        'userId' => $userId
    ]);
    
    if ($response->successful()) {
        $todos = $response->json();
        return view('api-examples.todos', compact('todos', 'userId'));
    }
    
    return view('api-examples.todos', [
        'todos' => [],
        'error' => 'Error al obtener tareas'
    ]);
}
```

### Ejercicio 2: Intermedio - CRUD Completo
**Objetivo:** Implementar operaciones CRUD completas.

**Pasos:**
1. GET - Listar posts
2. POST - Crear post
3. PUT - Actualizar post
4. DELETE - Eliminar post

**Solución:**
```php
// Actualizar post
public function updatePost(Request $request, $id)
{
    $validated = $request->validate([
        'title' => 'required|string',
        'body' => 'required|string'
    ]);
    
    $response = Http::put(
        config('services.jsonplaceholder.url') . '/posts/' . $id,
        $validated
    );
    
    if ($response->successful()) {
        return redirect()->back()->with('success', 'Post actualizado');
    }
    
    return redirect()->back()->with('error', 'Error al actualizar');
}

// Eliminar post
public function deletePost($id)
{
    $response = Http::delete(
        config('services.jsonplaceholder.url') . '/posts/' . $id
    );
    
    if ($response->successful()) {
        return redirect()->back()->with('success', 'Post eliminado');
    }
    
    return redirect()->back()->with('error', 'Error al eliminar');
}
```

### Ejercicio 3: Avanzado - Integración con API Real
**Objetivo:** Consumir una API real con autenticación.

**APIs sugeridas:**
- GitHub API (https://api.github.com)
- OpenWeatherMap (https://openweathermap.org/api)
- NewsAPI (https://newsapi.org)
- The Movie Database (https://www.themoviedb.org/documentation/api)

**Ejemplo con GitHub API:**
```php
public function getGitHubRepos($username)
{
    $response = Http::withHeaders([
        'Accept' => 'application/vnd.github.v3+json',
        'User-Agent' => 'Laravel-App'
    ])->get("https://api.github.com/users/{$username}/repos");
    
    if ($response->successful()) {
        $repos = $response->json();
        return view('github.repos', compact('repos', 'username'));
    }
    
    return view('github.repos', [
        'repos' => [],
        'error' => 'Usuario no encontrado'
    ]);
}
```

### Ejercicio 4: Proyecto Final - Dashboard de APIs
**Objetivo:** Crear un dashboard que consuma múltiples APIs.

**Requisitos:**
1. Consumir al menos 3 APIs diferentes
2. Usar requests concurrentes para optimizar
3. Implementar caché para reducir llamadas
4. Manejo robusto de errores
5. Interfaz responsive con Tailwind

**Estructura sugerida:**
```php
public function dashboard()
{
    try {
        $responses = Http::pool(fn ($pool) => [
            $pool->get('https://api.github.com/users/laravel'),
            $pool->get('https://api.openweathermap.org/data/2.5/weather?q=Lima&appid=' . config('services.weather.key')),
            $pool->get('https://newsapi.org/v2/top-headlines?country=us&apiKey=' . config('services.news.key')),
        ]);
        
        return view('dashboard.api', [
            'github' => $responses[0]->successful() ? $responses[0]->json() : null,
            'weather' => $responses[1]->successful() ? $responses[1]->json() : null,
            'news' => $responses[2]->successful() ? $responses[2]->json() : null,
        ]);
    } catch (Exception $e) {
        Log::error('Dashboard API Error: ' . $e->getMessage());
        return view('dashboard.api', ['error' => 'Error al cargar datos']);
    }
}
```

---

## Troubleshooting

### Problema 1: "Connection timeout"

**Síntoma:**
```
Illuminate\Http\Client\ConnectionException: cURL error 28: Connection timed out
```

**Soluciones:**
1. Aumentar timeout:
```php
Http::timeout(60)->get($url)
```

2. Verificar conectividad:
```bash
curl -I https://jsonplaceholder.typicode.com
```

3. Verificar firewall/proxy

### Problema 2: "SSL certificate problem"

**Síntoma:**
```
cURL error 60: SSL certificate problem: unable to get local issuer certificate
```

**Soluciones:**
1. Descargar certificados CA: https://curl.se/docs/caextract.html
2. Configurar en `php.ini`:
```ini
curl.cainfo = "C:\path\to\cacert.pem"
```

3. Solo para desarrollo (NO en producción):
```php
Http::withoutVerifying()->get($url)
```

### Problema 3: "Rate limit exceeded"

**Síntoma:**
```
HTTP 429 Too Many Requests
```

**Soluciones:**
1. Implementar caché:
```php
$users = Cache::remember('api_users', 3600, function () {
    return Http::get($url)->json();
});
```

2. Agregar delays entre requests:
```php
Http::retry(3, 1000)->get($url) // 1 segundo entre reintentos
```

3. Usar queue para requests masivos

### Problema 4: "Token inválido o expirado"

**Síntoma:**
```
HTTP 401 Unauthorized
```

**Soluciones:**
1. Verificar token en .env
2. Implementar refresh token:
```php
if ($response->unauthorized()) {
    $newToken = $this->refreshToken();
    $response = Http::withToken($newToken)->get($url);
}
```

3. Verificar formato del token (Bearer, API Key, etc.)

### Problema 5: "JSON decode error"

**Síntoma:**
```
Syntax error, malformed JSON
```

**Soluciones:**
1. Verificar respuesta antes de decodificar:
```php
if ($response->successful()) {
    $data = $response->json();
} else {
    Log::error('Response body: ' . $response->body());
}
```

2. Usar `body()` para ver respuesta raw:
```php
dd($response->body());
```

3. Verificar Content-Type de la respuesta

---

## Recursos Adicionales

### APIs Públicas para Practicar
- **JSONPlaceholder** - https://jsonplaceholder.typicode.com
- **ReqRes** - https://reqres.in
- **Random User** - https://randomuser.me
- **Dog API** - https://dog.ceo/dog-api
- **Cat Facts** - https://catfact.ninja

### Documentación Oficial
- Laravel HTTP Client: https://laravel.com/docs/http-client
- Guzzle Documentation: https://docs.guzzlephp.org
- HTTP Status Codes: https://httpstatuses.com

### Herramientas Útiles
- **Postman** - Testing de APIs
- **Insomnia** - Cliente REST
- **HTTPie** - Cliente HTTP en terminal
- **JSON Formatter** - Extensión de navegador

---

## Checklist de Implementación

### Para el Docente
- [ ] Revisar todos los ejemplos funcionan correctamente
- [ ] Preparar presentación con diapositivas
- [ ] Configurar proyector/pantalla compartida
- [ ] Tener backup de código en caso de problemas
- [ ] Preparar ejercicios adicionales
- [ ] Crear rúbrica de evaluación

### Para el Estudiante
- [ ] Clonar repositorio del proyecto
- [ ] Ejecutar `composer install`
- [ ] Configurar `.env` con variables necesarias
- [ ] Ejecutar migraciones
- [ ] Crear usuario de prueba
- [ ] Acceder a `/api-examples`
- [ ] Probar cada ejemplo
- [ ] Completar ejercicios asignados

---

## Evaluación Sugerida

### Criterios de Evaluación (100 puntos)

**1. Comprensión de Conceptos (30 puntos)**
- Explica correctamente qué es una API REST (10 pts)
- Diferencia entre métodos HTTP (10 pts)
- Comprende códigos de estado HTTP (10 pts)

**2. Implementación Técnica (40 puntos)**
- GET request funcional (10 pts)
- POST request con validación (10 pts)
- Manejo de errores apropiado (10 pts)
- Configuración de timeout/retry (10 pts)

**3. Buenas Prácticas (20 puntos)**
- Uso de variables de entorno (5 pts)
- Código limpio y comentado (5 pts)
- Manejo de excepciones (5 pts)
- Seguridad (tokens, HTTPS) (5 pts)

**4. Proyecto Final (10 puntos)**
- Creatividad en la implementación (5 pts)
- Interfaz de usuario funcional (5 pts)

---

## Conclusión

Esta guía proporciona una base sólida para enseñar el consumo de APIs externas con Laravel HTTP Client. Los ejemplos están diseñados para ser progresivos, comenzando con conceptos básicos y avanzando hacia implementaciones más complejas.

**Puntos clave para recordar:**
1. Siempre usar variables de entorno para configuración
2. Implementar manejo robusto de errores
3. Considerar timeouts y retries
4. Nunca exponer credenciales en el código
5. Usar HTTPS en producción
6. Implementar caché cuando sea apropiado
7. Respetar límites de rate limiting

**Próximos pasos:**
- Explorar APIs reales con autenticación
- Implementar webhooks
- Crear servicios reutilizables
- Integrar con colas de Laravel
- Implementar testing de APIs

---

## Contacto y Soporte

Para preguntas o problemas con la implementación:
- Revisar documentación oficial de Laravel
- Consultar Stack Overflow
- Revisar issues en GitHub del proyecto
- Contactar al docente durante horario de consulta

**¡Éxito en la enseñanza y aprendizaje del consumo de APIs con Laravel!** 🚀
