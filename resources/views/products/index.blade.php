@extends('layouts.app')

@section('content')

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

                        <form action="{{ route('products.destroy', $product) }}"
                              method="POST"
                              style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit">🗑 Eliminar</button>
                        </form>
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
