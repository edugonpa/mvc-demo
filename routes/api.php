<?php

use App\Http\Controllers\Api\ProductoController;
use Illuminate\Support\Facades\Route;

Route::apiResource('productos', ProductoController::class);
