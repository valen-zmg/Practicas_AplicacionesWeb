<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@utt.test'],
            ['name' => 'Administrador', 'password' => 'Password123!', 'role' => User::ADMIN]
        );
        User::firstOrCreate(
            ['email' => 'empleado@utt.test'],
            ['name' => 'Empleado', 'password' => 'Password123!', 'role' => User::EMPLEADO]
        );

        $catalogo = [
            'Electrónica' => ['Teclado mecánico', 'Mouse inalámbrico', 'Monitor 24"', 'Audífonos', 'Webcam HD', 'Memoria USB 64GB', 'Cargador USB-C', 'Bocina Bluetooth', 'Disco SSD 500GB', 'Hub USB'],
            'Papelería' => ['Cuaderno profesional', 'Caja de plumas', 'Marcatextos', 'Calculadora científica', 'Carpeta A4', 'Pegamento', 'Tijeras', 'Post-it', 'Regla 30 cm', 'Grapadora'],
            'Hogar' => ['Lámpara LED', 'Cojín decorativo', 'Taza cerámica', 'Organizador', 'Reloj de pared', 'Tapete', 'Vaso térmico', 'Cesto', 'Espejo', 'Portarretratos'],
            'Deportes' => ['Balón de fútbol', 'Cuerda para saltar', 'Botella deportiva', 'Guantes de gimnasio', 'Tapete de yoga', 'Pesas 5kg', 'Raqueta', 'Gorra', 'Muñequeras', 'Mochila deportiva'],
            'Juguetes' => ['Rompecabezas', 'Set de bloques', 'Muñeca', 'Carro a escala', 'Juego de mesa', 'Peluche', 'Pelota', 'Yo-yo', 'Plastilina', 'Cubo de Rubik'],
            'Alimentos' => ['Café molido', 'Galletas', 'Té verde', 'Cereal', 'Barra de granola', 'Miel', 'Chocolate', 'Nueces', 'Aceite de oliva', 'Pasta'],
        ];

        mt_srand(42); // datos reproducibles
        $n = 0;

        foreach ($catalogo as $nombreCategoria => $productos) {
            $categoria = Categoria::firstOrCreate(['nombre' => $nombreCategoria]);

            foreach ($productos as $nombre) {
                $n++;
                $existencias = mt_rand(0, 4) === 0 ? 0 : mt_rand(1, 80);

                $producto = Producto::firstOrNew(['nombre' => $nombre]);

                if (! $producto->exists) {
                    $producto->fill([
                        'descripcion' => "{$nombre}: artículo de la categoría {$nombreCategoria}, ideal para uso diario.",
                        'categoria_id' => $categoria->id,
                        'precio' => mt_rand(1500, 250000) / 100,
                        'existencias' => $existencias,
                        'estado' => $existencias > 0 ? 'disponible' : 'agotado',
                    ]);
                    $producto->created_at = now()->subDays(mt_rand(0, 120))->subMinutes($n);
                    $producto->save();
                }
            }
        }
    }
}
