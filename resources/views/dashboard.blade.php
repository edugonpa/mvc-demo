<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Mensajes flash de error --}}
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    ❌ {{ session('error') }}
                </div>
            @endif

            {{-- Mensajes flash de éxito --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}
                    
                    <p class="mt-4">
                        <strong>Tu rol:</strong> {{ auth()->user()->role }}
                    </p>
                    
                    @if(auth()->user()->isAdmin())
                        <p class="mt-2 text-green-600 dark:text-green-400">
                            ✅ Tienes permisos de administrador
                        </p>
                    @else
                        <p class="mt-2 text-blue-600 dark:text-blue-400">
                            ℹ️ Eres un usuario regular
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
