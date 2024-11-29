<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HelloWorldController extends Controller
{
    /**
     * Lista todos los ficheros de la carpeta storage/app.
     *
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     * - contenido: Un array con los nombres de los ficheros.
     */
    public function index()
    {
        // obtener los archivos en la carpeta storage/app
        $files = Storage::allFiles();

        // Responder con un Json qie incluye msj y su contenido
        return response()->json([
            'mensaje' => 'Listado de ficheros', 
            'contenido' =>$files,
        ], 200);
    }

     /**
     * Recibe por parámetro el nombre de fichero y el contenido. Devuelve un JSON con el resultado de la operación.
     * Si el fichero ya existe, devuelve un 409.
     *
     * @param filename Parámetro con el nombre del fichero. Devuelve 422 si no hay parámetro.
     * @param content Contenido del fichero. Devuelve 422 si no hay parámetro.
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     */
    public function store(Request $request)
    {
             // Validar los parámetros de entrada
            $validated = $request->validate([
                'filename' => 'required|string',
                'content' => 'required|string',
            ]);

            // Comprobar si el archivo ya existe
            if (Storage::exists($validated['filename'])) {
                // Si el archivo ya existe, devolver un error 409
                return response()->json([
                    'mensaje' => 'El archivo ya existe',  // Con punto final
                ], 409);
            }

            // Guardar el archivo en la carpeta storage/app
            Storage::put($validated['filename'], $validated['content']);

            // Responder con un mensaje de éxito y código 201 (Creado)
            return response()->json([
                'mensaje' => 'Guardado con éxito',
            ], 200);
    }

     /**
     * Recibe por parámetro el nombre de fichero y devuelve un JSON con su contenido
     *
     * @param name Parámetro con el nombre del fichero.
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     * - contenido: El contenido del fichero si se ha leído con éxito.
     */
    public function show(string $filename)
    {
        // Verificar si el archivo existe
        if (!Storage::exists($filename)) {
            return response()->json([
                'mensaje' => 'Archivo no encontrado',
            ], 404);  // Si no existe, devolver 404
        }

        // Leer el contenido del archivo
        $content = Storage::get($filename);

        // Responder con el contenido del archivo
        return response()->json([
            'mensaje' => 'Archivo leído con éxito',
            'contenido' => $content,
        ], 200);
    }

    /**
     * Recibe por parámetro el nombre de fichero, el contenido y actualiza el fichero.
     * Devuelve un JSON con el resultado de la operación.
     * Si el fichero no existe devuelve un 404.
     *
     * @param filename Parámetro con el nombre del fichero. Devuelve 422 si no hay parámetro.
     * @param content Contenido del fichero. Devuelve 422 si no hay parámetro.
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     */
    public function update(Request $request, string $filename)
    {
           // Validar que el contenido sea una cadena
    $validated = $request->validate([
        'content' => 'required|string',  // 'content' debe ser una cadena
    ]);

    // Verificar si el archivo existe en el almacenamiento
    if (!Storage::exists($filename)) {
        // Si el archivo no existe, devolver error 404
        return response()->json([
            'mensaje' => 'El archivo no existe',
        ], 404);
    }

    // Si el archivo existe, actualizar su contenido
    Storage::put($filename, $request->content);

    // Devolver una respuesta JSON indicando que el archivo fue actualizado correctamente
    return response()->json([
        'mensaje' => 'Actualizado con éxito',
    ], 200);
    }

    /**
     * Recibe por parámetro el nombre de ficher y lo elimina.
     * Si el fichero no existe devuelve un 404.
     *
     * @param filename Parámetro con el nombre del fichero. Devuelve 422 si no hay parámetro.
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     */
    public function destroy(string $filename)
    {
        // Verificar si el archivo existe
        if (!Storage::exists($filename)) {
            // Si no existe, devolver un error 404
            return response()->json([
                'mensaje' => 'El archivo no existe',
            ], 404);
        }

        // Eliminar el archivo
        Storage::delete($filename);

        // Responder con un mensaje de éxito
        return response()->json([
            'mensaje' => 'Eliminado con éxito',
        ], 200); 
    }
}
