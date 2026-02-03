<?php

namespace Tests\Feature\Api;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Listar todos los productos
     */
    public function test_puede_listar_todos_los_productos(): void
    {
        // Crear productos de prueba
        Product::factory()->count(3)->create();

        // Hacer petición GET
        $response = $this->getJson('/api/productos');

        // Verificar respuesta
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'nombre',
                        'precio',
                        'category_id',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Productos obtenidos exitosamente'
            ]);

        // Verificar que hay 3 productos
        $this->assertCount(3, $response->json('data'));
    }

    /**
     * Test: Crear un producto exitosamente
     */
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

    /**
     * Test: Validación al crear producto sin datos requeridos
     */
    public function test_validacion_al_crear_producto_sin_datos(): void
    {
        $response = $this->postJson('/api/productos', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors' => [
                    'nombre',
                    'precio'
                ]
            ])
            ->assertJson([
                'success' => false,
                'message' => 'Error de validación'
            ]);
    }

    /**
     * Test: Validación de precio negativo
     */
    public function test_validacion_precio_negativo(): void
    {
        $data = [
            'nombre' => 'Producto Test',
            'precio' => -10
        ];

        $response = $this->postJson('/api/productos', $data);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors'
            ]);
    }

    /**
     * Test: Mostrar un producto específico
     */
    public function test_puede_mostrar_un_producto(): void
    {
        $producto = Product::factory()->create([
            'nombre' => 'mouse',
            'precio' => 25.50
        ]);

        $response = $this->getJson("/api/productos/{$producto->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Producto obtenido exitosamente',
                'data' => [
                    'id' => $producto->id,
                    'nombre' => 'MOUSE', // Mayúsculas
                    'precio' => '25.50'  // 2 decimales
                ]
            ]);
    }

    /**
     * Test: Error 404 al buscar producto inexistente
     */
    public function test_error_404_producto_inexistente(): void
    {
        $response = $this->getJson('/api/productos/9999');

        $response->assertStatus(404);
    }

    /**
     * Test: Actualizar un producto
     */
    public function test_puede_actualizar_un_producto(): void
    {
        $producto = Product::factory()->create([
            'nombre' => 'teclado',
            'precio' => 45.00
        ]);

        $dataActualizada = [
            'nombre' => 'teclado mecánico',
            'precio' => 89.99
        ];

        $response = $this->putJson("/api/productos/{$producto->id}", $dataActualizada);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Producto actualizado exitosamente',
                'data' => [
                    'id' => $producto->id,
                    'nombre' => 'TECLADO MECÁNICO',
                    'precio' => '89.99'
                ]
            ]);

        // Verificar en base de datos
        $this->assertDatabaseHas('products', [
            'id' => $producto->id,
            'nombre' => 'teclado mecánico',
            'precio' => 89.99
        ]);
    }

    /**
     * Test: Eliminar un producto
     */
    public function test_puede_eliminar_un_producto(): void
    {
        $producto = Product::factory()->create();

        $response = $this->deleteJson("/api/productos/{$producto->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Producto eliminado exitosamente',
                'data' => null
            ]);

        // Verificar que fue eliminado
        $this->assertDatabaseMissing('products', [
            'id' => $producto->id
        ]);
    }

    /**
     * Test: Formato de respuesta homogéneo
     */
    public function test_formato_respuesta_homogeneo(): void
    {
        Product::factory()->create();

        $response = $this->getJson('/api/productos');

        // Verificar estructura consistente
        $response->assertJsonStructure([
            'success',
            'message',
            'data'
        ]);

        // Verificar tipos de datos
        $json = $response->json();
        $this->assertIsBool($json['success']);
        $this->assertIsString($json['message']);
        $this->assertIsArray($json['data']);
    }

    /**
     * Test: ProductoResource formatea correctamente
     */
    public function test_producto_resource_formatea_correctamente(): void
    {
        $producto = Product::factory()->create([
            'nombre' => 'monitor',
            'precio' => 299.9
        ]);

        $response = $this->getJson("/api/productos/{$producto->id}");

        $data = $response->json('data');

        // Verificar formato de nombre (mayúsculas)
        $this->assertEquals('MONITOR', $data['nombre']);

        // Verificar formato de precio (2 decimales)
        $this->assertEquals('299.90', $data['precio']);
        $this->assertMatchesRegularExpression('/^\d+\.\d{2}$/', $data['precio']);
    }
}
