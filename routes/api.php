<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HelloWorldController;
use App\Http\Controllers\JsonController;
use App\Http\Controllers\CsvController;

Route::apiResource('hello', HelloWorldController::class);

Route::apiResource('json', JsonController::class);


// Ruta para listar todos los archivos CSV
Route::get('/csv', [CsvController::class, 'index']);

// Ruta para mostrar el contenido de un archivo CSV
Route::get('/csv/{filename}', [CsvController::class, 'show']);
Route::get('/csv/{id}', [CsvController::class, 'show']);


// Ruta para almacenar un nuevo archivo CSV
Route::post('/csv', [CsvController::class, 'store']);

// Ruta para actualizar un archivo CSV
Route::put('/csv/{filename}', [CsvController::class, 'update']);

// Ruta para eliminar un archivo CSV
Route::delete('/csv/{filename}', [CsvController::class, 'destroy']);






