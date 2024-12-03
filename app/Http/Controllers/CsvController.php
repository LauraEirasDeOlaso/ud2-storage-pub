<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class CsvController extends Controller
{
    /**
     * Lista todos los ficheros CSV de la carpeta storage/app.
     *
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     * - contenido: Un array con los nombres de los ficheros.
     */
    public function index()
    {
        $files = Storage::files();
        $csvFiles = array_filter($files, function ($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'csv';
        });

        return response()->json([
            'mensaje' => 'Listado de ficheros',
            'contenido' => array_map(fn($file) => basename($file), $csvFiles),
        ]);
    }




   /**
     * Recibe por parámetro el nombre de fichero y el contenido CSV y crea un nuevo fichero con ese nombre y contenido en storage/app. 
     * Devuelve un JSON con el resultado de la operación.
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
        $request->validate([
            'filename' => 'required|string',
            'content' => 'required|string',
        ]);

        if (Storage::exists($request->filename)) {
            return response()->json([
                'mensaje' => 'El fichero ya existe',
            ], 409);
        }

        Storage::put($request->filename, $request->content);

        return response()->json([
            'mensaje' => 'Guardado con éxito',
        ]);
    }

    /**
     * Recibe por parámetro el nombre de un fichero CSV el nombre de fichero y devuelve un JSON con su contenido.
     * Si el fichero no existe devuelve un 404.
     * Hay que hacer uso lo visto en la presentación CSV to JSON.
     *
     * @param name Parámetro con el nombre del fichero CSV.
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     * - contenido: El contenido del fichero si se ha leído con éxito.
     */
    public function show(string $id)
    {
          // Verifica si el archivo existe en el almacenamiento
        if (!Storage::exists($id)) {
            return response()->json(['mensaje' => 'El fichero no existe'], 404);
        }

        // Obtiene el contenido del archivo
        $content = Storage::get($id);

        // Divide el contenido en líneas y quita espacios en blanco adicionales
        $rows = explode("\n", trim($content));

        // Extrae los encabezados (primera línea)
        $header = str_getcsv(array_shift($rows));

        // Convierte las filas en datos asociativos
        try {
            $data = array_map(fn($row) => array_combine($header, str_getcsv($row)), $rows);
        } catch (\Throwable $e) {
            // Devuelve un error si las filas no coinciden con los encabezados
            return response()->json([
                'mensaje' => 'Error al procesar el archivo CSV. Revisa su formato.',
                'contenido' => [],
            ], 400);
        }

        // Devuelve una respuesta JSON con los datos
        return response()->json([
            'mensaje' => 'Fichero leído con éxito',
            'contenido' => $data,
        ], 200);
    }


   /**
     * Recibe por parámetro el nombre de fichero, el contenido CSV y actualiza el fichero CSV. 
     * Devuelve un JSON con el resultado de la operación.
     * Si el fichero no existe devuelve un 404.
     * Si el contenido no es un JSON válido, devuelve un 415.
     * 
     * @param filename Parámetro con el nombre del fichero. Devuelve 422 si no hay parámetro.
     * @param content Contenido del fichero. Devuelve 422 si no hay parámetro.
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        if (!Storage::exists($id)) {
            return response()->json([
                'mensaje' => 'El fichero no existe',
            ], 404);
        }

        $csvData = @str_getcsv($request->content);
        if ($csvData === false) {
            return response()->json([
                'mensaje' => 'Formato de CSV no válido',
            ], 415);
        }

        Storage::put($id, $request->content);

        return response()->json([
            'mensaje' => 'Fichero actualizado exitosamente',
        ]);
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
    public function destroy(string $id)
    {
        if (!Storage::exists($id)) {
            return response()->json([ 
                'mensaje' => 'El fichero no existe',
            ], 404);
        }

        Storage::delete($id);

        return response()->json([
            'mensaje' => 'Fichero eliminado exitosamente',
        ]);
    }
    
}