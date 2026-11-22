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
            ['email' => 'admin@gmail.com'],
            ['name' => 'Administrador', 'password' => 'Password123!', 'role' => User::ADMIN]
        );
        User::firstOrCreate(
            ['email' => 'empleado@gmail.com'],
            ['name' => 'Empleado', 'password' => '123456789', 'role' => User::EMPLEADO]
        );

        $catalogo = [
            'Electrónica' => [
                ['Teclado mecánico', 899.00],
                ['Mouse inalámbrico', 249.00],
                ['Monitor 24 pulgadas', 2499.00],
                ['Audífonos', 349.00],
                ['Webcam HD', 599.00],
                ['Memoria USB 64GB', 129.00],
                ['Cargador USB-C', 199.00],
                ['Bocina Bluetooth', 449.00],
                ['Disco SSD 500GB', 799.00],
                ['Hub USB', 229.00],
            ],
            'Papelería' => [
                ['Cuaderno profesional', 38.00],
                ['Caja de plumas', 45.00],
                ['Marcatextos', 12.00],
                ['Calculadora científica', 289.00],
                ['Carpeta A4', 15.00],
                ['Pegamento', 18.00],
                ['Tijeras', 32.00],
                ['Post-it', 28.00],
                ['Regla 30 cm', 10.00],
                ['Grapadora', 65.00],
            ],
            'Hogar' => [
                ['Lámpara LED', 189.00],
                ['Cojín decorativo', 149.00],
                ['Taza cerámica', 59.00],
                ['Organizador', 99.00],
                ['Reloj de pared', 179.00],
                ['Tapete', 249.00],
                ['Vaso térmico', 189.00],
                ['Cesto', 79.00],
                ['Espejo', 229.00],
                ['Portarretratos', 69.00],
            ],
            'Deportes' => [
                ['Balón de fútbol', 299.00],
                ['Cuerda para saltar', 79.00],
                ['Botella deportiva', 99.00],
                ['Guantes de gimnasio', 169.00],
                ['Tapete de yoga', 249.00],
                ['Pesas 5kg', 349.00],
                ['Raqueta', 399.00],
                ['Gorra', 129.00],
                ['Muñequeras', 59.00],
                ['Mochila deportiva', 349.00],
            ],
            'Juguetes' => [
                ['Rompecabezas', 149.00],
                ['Set de bloques', 299.00],
                ['Muñeca', 199.00],
                ['Carro a escala', 89.00],
                ['Juego de mesa', 349.00],
                ['Peluche', 179.00],
                ['Pelota', 49.00],
                ['Yo-yo', 35.00],
                ['Plastilina', 39.00],
                ['Cubo de Rubik', 129.00],
            ],
            'Alimentos' => [
                ['Café molido', 89.00],
                ['Galletas', 24.00],
                ['Té verde', 45.00],
                ['Cereal', 62.00],
                ['Barra de granola', 18.00],
                ['Miel', 85.00],
                ['Chocolate', 32.00],
                ['Nueces', 79.00],
                ['Aceite de oliva', 149.00],
                ['Pasta', 16.00],
            ],
        ];

        mt_srand(42); // las existencias y fechas son reproducibles
        $n = 0;

        foreach ($catalogo as $nombreCategoria => $productos) {
            $categoria = Categoria::firstOrCreate(['nombre' => $nombreCategoria]);

            foreach ($productos as [$nombre, $precio]) {
                $n++;
                $existencias = mt_rand(0, 4) === 0 ? 0 : mt_rand(1, 80);

                $producto = Producto::firstOrNew(['nombre' => $nombre]);

                if (! $producto->exists) {
                    $producto->fill([
                        'descripcion' => "{$nombre}: artículo de la categoría {$nombreCategoria}, ideal para uso diario.",
                        'categoria_id' => $categoria->id,
                        'precio' => $precio,
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
