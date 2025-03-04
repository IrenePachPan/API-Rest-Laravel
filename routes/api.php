<?php

use Illuminate\Http\Request; //este no se usaria por ahora
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\ProductosController;

Route::get('/productos', [ProductosController::class, 'index']);

Route::get('/productos/{nombre}', [ProductosController::class, 'show']);

Route::post('/productos', [ProductosController::class, 'store']);

Route::post('/productos/all', [ProductosController::class, 'storeAll']);

Route::put('/productos/{nombre}', [ProductosController::class, 'update']);

Route::patch('/productos/{nombre}', [ProductosController::class, 'updatePartial']);

Route::delete('/productos/{nombre}', [ProductosController::class, 'destroy']);