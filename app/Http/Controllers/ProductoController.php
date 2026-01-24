<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index() {
        // return "hola desde el controller";
        //$modelo = new Producto();
        //$productos = $modelo->obtenerProductos();
        //$productos = ['caja de leche', 'arroz', 'hariha'];
        //$productos = Producto::all();
        //return view('simulacion', compact('productos'));

        // $usuarios = collect(['Ana', 'Luis', 'Carlos']);

        // $usuarios
        //     ->map(fn($u) => strtoupper($u))
        //     ->filter(fn($u) => strlen($u) > 4)
        //     ->each(fn($u) => print($u . PHP_EOL));

        // return $usuarios;

        //return view('usuarios', compact('usuarios'));


        // $productos = Producto::all()
        //     ->where('precio', '>=', 1000)
        //     ->pluck('nombre')
        //     ->sortDesc()
        //     ->values();
        // return $productos;
    }
}
