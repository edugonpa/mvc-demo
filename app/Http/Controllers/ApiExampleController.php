<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * ApiExampleController
 * 
 * Controlador de ejemplo para demostrar el consumo de APIs externas
 * usando Laravel HTTP Client.
 * 
 * Incluye ejemplos de:
 * - GET requests

 */
class ApiExampleController extends Controller
{
    /**
     * Página principal con menú de ejemplos
     */
    public function index()
    {
        return view('api-examples.index');
    }

    /**
     * EJEMPLO 1: GET Request Simple
     * 
     * Obtiene una lista de usuarios desde JSONPlaceholder
     * Demuestra: Request GET básico, manejo de respuesta JSON
     */
    public function getUsers()
    {
        try {
            // Realizar petición GET
            $response = Http::get(config('services.jsonplaceholder.url') . '/users');

            // Verificar si la petición fue exitosa
            if ($response->successful()) {
                $users = $response->json();

                //return $users;
                
                return view('api-examples.get-users', [
                    'users' => $users,
                    'status' => $response->status(),
                    'success' => true
                ]);
            }

            // Si no fue exitosa, manejar el error
            return view('api-examples.get-users', [
                'users' => [],
                'error' => 'Error al obtener usuarios: ' . $response->status(),
                'success' => false
            ]);

        } catch (Exception $e) {
            Log::error('Error en getUsers: ' . $e->getMessage());
            
            return view('api-examples.get-users', [
                'users' => [],
                'error' => 'Excepción: ' . $e->getMessage(),
                'success' => false
            ]);
        }
    }
}