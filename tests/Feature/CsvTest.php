<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class CsvTest extends TestCase
{
    public function testIndex()
    {
        // Simula el almacenamiento en disco
        Storage::fake('local');

        // Crear ficheros CSV falsos
        Storage::put('file1.csv', 'header1,header2\nvalue1,value2');
        Storage::put('file2.csv', 'header1,header2\nvalue1,value2');
        Storage::put('valid.json', json_encode(['key' => 'value']));

        // Hacer la petición GET a la API
        $response = $this->getJson('/api/csv');

        // Verificar que la respuesta es la esperada
        $response->assertStatus(200)
                 ->assertJson([
                     'mensaje' => 'Listado de ficheros',
                     'contenido' => ['file1.csv', 'file2.csv'],
                 ]);
    }

    public function testShow()
    {
        Storage::fake('local');

        // Crear un archivo CSV con datos conocidos
        Storage::put('existingfile.csv', "header1,header2\nvalue1,value2");// Puta madre con las comillaassss

        $response = $this->getJson('/api/csv/existingfile.csv');

        $response->assertStatus(200)
                    ->assertJson([
                        'mensaje' => 'Fichero leído con éxito',
                        'contenido' => [
                            ['header1' => 'value1', 'header2' => 'value2']
                        ]
                    ]);
    }

    public function testStore()
    {
        // Simula el almacenamiento en disco
        Storage::fake('local');

        // Datos para el nuevo archivo
        $data = [
            'filename' => 'newfile.csv',
            'content' => "header1,header2\nvalue1,value2",
        ];

        // Hacer la petición POST para guardar el archivo
        $response = $this->postJson('/api/csv', $data);

        // Verificar que la respuesta es la esperada
        $response->assertStatus(200)
                 ->assertJson([
                     'mensaje' => 'Guardado con éxito',
                 ]);

        // Verificar que el archivo se haya guardado correctamente
        Storage::disk('local')->assertExists('newfile.csv');
    }

    public function testUpdate()
    {
           // Simula el almacenamiento en disco
           Storage::fake('local');

           // Crear un archivo CSV con datos iniciales
           Storage::put('existingfile.csv', "header1,header2\nvalue1,value2");
   
           // Nuevos datos para el archivo
           $data = [
               'content' => "header1,header2\nvalue3,value4",
           ];
   
           // Hacer la petición PUT para actualizar el archivo
           $response = $this->putJson('/api/csv/existingfile.csv', $data);
   
           // Verificar que la respuesta es la esperada
           $response->assertStatus(200)
                    ->assertJson([
                        'mensaje' => 'Fichero actualizado exitosamente',
                    ]);
   
           // Verificar que el contenido del archivo se haya actualizado correctamente
           $content = Storage::get('existingfile.csv');
           $this->assertStringContainsString('value3,value4', $content);
    }

    public function testDestroy() 
    {
           // Simula el almacenamiento en disco
           Storage::fake('local');

           // Crear un archivo CSV
           Storage::put('existingfile.csv', "header1,header2\nvalue1,value2");
   
           // Hacer la petición DELETE para eliminar el archivo
           $response = $this->deleteJson('/api/csv/existingfile.csv');
   
           // Verificar que la respuesta es la esperada
           $response->assertStatus(200)
                    ->assertJson([
                        'mensaje' => 'Fichero eliminado exitosamente',
                    ]);
   
           // Verificar que el archivo haya sido eliminado
           Storage::disk('local')->assertMissing('existingfile.csv');
       
    }
}