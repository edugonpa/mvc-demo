<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ejemplo 1: GET Request Simple') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <a href="{{ route('api-examples.index') }}" class="text-blue-500 hover:text-blue-700">
                            ← Volver a Ejemplos
                        </a>
                    </div>

                    <h3 class="text-2xl font-bold mb-4">GET Request Simple</h3>

                    {{-- DEBUG INFO --}}
                    @if(isset($debug))
                        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-300 rounded-lg">
                            <h4 class="font-semibold mb-2 text-yellow-800">🔍 Información de Debug:</h4>
                            <pre class="text-xs">{{ print_r($debug, true) }}</pre>
                        </div>
                    @endif
                    
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h4 class="font-semibold mb-2">Código utilizado:</h4>
                        <pre class="bg-gray-800 text-white p-4 rounded overflow-x-auto"><code>$response = Http::get(config('services.jsonplaceholder.url') . '/users');

if ($response->successful()) {
    $users = $response->json();
    // Procesar usuarios
}</code></pre>
                    </div>

                    @if(isset($success) && $success)
                        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded">
                            <p class="text-green-800">
                                ✓ Petición exitosa - Status: {{ $status }}
                            </p>
                            <p class="text-green-600 text-sm mt-2">
                                Total de usuarios: {{ count($users) }}
                            </p>
                        </div>

                        {{-- DEBUG: Ver estructura de datos --}}
                        <div class="mb-4 p-4 bg-purple-50 border border-purple-200 rounded">
                            <h4 class="font-semibold mb-2 text-purple-800">🔍 Datos recibidos:</h4>
                            <pre class="text-xs overflow-x-auto">{{ json_encode($users, JSON_PRETTY_PRINT) }}</pre>
                        </div>

                        <h4 class="text-lg font-semibold mb-4">Usuarios obtenidos ({{ count($users) }}):</h4>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ciudad</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($users as $user)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $user['id'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $user['name'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $user['email'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $user['address']['city'] ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded">
                            <p class="text-red-800">
                                ✗ Error: {{ $error ?? 'Error desconocido' }}
                            </p>
                        </div>
                    @endif

                    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded">
                        <h4 class="font-semibold text-blue-800 mb-2">💡 Conceptos Clave</h4>
                        <ul class="list-disc list-inside text-blue-700 space-y-1">
                            <li><strong>Http::get()</strong> - Realiza una petición GET</li>
                            <li><strong>successful()</strong> - Verifica si el status es 2xx</li>
                            <li><strong>json()</strong> - Convierte la respuesta a array PHP</li>
                            <li><strong>status()</strong> - Obtiene el código de estado HTTP</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
