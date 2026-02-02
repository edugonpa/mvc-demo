<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ejemplos de Consumo de APIs Externas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold mb-6">Laravel HTTP Client - Ejemplos Prácticos</h3>

                    <p class="mb-6 text-gray-600">
                        Esta sección contiene ejemplos prácticos de cómo consumir APIs externas usando Laravel HTTP
                        Client.
                        Cada ejemplo demuestra diferentes características y mejores prácticas.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Ejemplo 1 -->
                        <div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition">
                            <h4 class="text-lg font-semibold mb-2 text-blue-600">1. GET Request Simple</h4>
                            <p class="text-gray-600 mb-4">Obtener lista de usuarios desde una API pública.</p>
                            <a href="{{ route('api-examples.get-users') }}"
                                class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                Ver Ejemplo
                            </a>
                        </div>
                    </div>

                    <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <h4 class="font-semibold text-blue-800 mb-2">📚 Nota para Estudiantes</h4>
                        <p class="text-blue-700">
                            Estos ejemplos utilizan <strong>JSONPlaceholder</strong>, una API REST pública gratuita 
                            para pruebas y prototipos. Los datos no se guardan realmente, pero las respuestas 
                            simulan operaciones reales de una API.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
