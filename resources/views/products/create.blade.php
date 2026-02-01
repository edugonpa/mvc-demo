@extends('layouts.app')

@section('content')

<h2>Crear producto</h2>

<form action="{{ route('products.store') }}" method="POST">
    @csrf

    <div>
        <label>Nombre:</label><br>
        <input type="text" name="nombre" value="{{ old('nombre') }}">
        @error('nombre')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>

    <br>

    <div>
        <label>Precio:</label><br>
        <input type="number" name="precio" value="{{ old('precio') }}">
        @error('precio')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>

    <br>

    <div>
        <label>Categoría:</label><br>
        <select name="category_id">
            <option value="">-- Seleccione una categoría --</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}"
                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->nombre }}
                </option>
            @endforeach
        </select>

        @error('category_id')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>

    <br>

    <button type="submit">Guardar</button>
    <a href="{{ route('products.index') }}">Cancelar</a>
</form>

@endsection
