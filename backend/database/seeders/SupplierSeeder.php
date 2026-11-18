<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'institution_id' => 1,
                'name' => 'Distribuidora Farmacéutica Boliviana S.A.',
                'contact_person' => 'Roberto García',
                'email' => 'ventas@difarb.bo',
                'phone' => '+591 2 2345678',
                'address' => 'Av. Arce 2456, La Paz',
                'nit' => '1025478963',
                'status' => 'active',
            ],
            [
                'institution_id' => 1,
                'name' => 'Ingenio Farmacéutico Santa Cruz',
                'contact_person' => 'María López',
                'email' => 'pedidos@infarma.bo',
                'phone' => '+591 3 3456789',
                'address' => 'Av. Ballivián 1890, Santa Cruz',
                'nit' => '1234567890',
                'status' => 'active',
            ],
            [
                'institution_id' => 1,
                'name' => 'MedSuministros Bolivia',
                'contact_person' => 'Carlos Mendoza',
                'email' => 'ventas@medsuministros.bo',
                'phone' => '+591 2 2789012',
                'address' => 'Calle Colón 567, La Paz',
                'nit' => '3456789012',
                'status' => 'active',
            ],
            [
                'institution_id' => 1,
                'name' => 'Droguería Internacional S.R.L.',
                'contact_person' => 'Ana Ramírez',
                'email' => 'compras@drogintl.bo',
                'phone' => '+591 4 4567890',
                'address' => 'Av. Freedom 3456, Cochabamba',
                'nit' => '5678901234',
                'status' => 'active',
            ],
            [
                'institution_id' => 1,
                'name' => 'Laboratorys Unidos del Altiplano',
                'contact_person' => 'Fernando Torres',
                'email' => 'contacto@labunal.bo',
                'phone' => '+591 2 2890123',
                'address' => 'Zona Sur, Calle 12 de Octubre 890',
                'nit' => '7890123456',
                'status' => 'active',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
