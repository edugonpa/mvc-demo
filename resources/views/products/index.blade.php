@extends('layouts.app')

@section('content')

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

    <a href="{{ route('products.create') }}">➕ Crear producto</a>

    <br><br>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->nombre }}</td>
                    <td>${{ $product->precio }}</td>
                    <td>
                        <a href="{{ route('products.edit', $product) }}">✏️ Editar</a>

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
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No hay productos registrados</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <br>

    {{ $products->links() }}

@endsection
