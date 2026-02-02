<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index()
    {
        return response()->json(Product::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string',
            'precio' => 'required|numeric'
        ]);

        $producto = Product::create($data);

        return response()->json($producto, 201);
    }

    public function show(Product $producto)
    {
        return response()->json($producto);
    }

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

    public function destroy(Product $producto)
    {
        $producto->delete();

        return response()->json(null, 204);
    }
}
