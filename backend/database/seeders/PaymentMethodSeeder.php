<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $institutionId = DB::table('institutions')->first()->id;

        $paymentMethods = [
            ['institution_id' => $institutionId, 'name' => 'Efectivo', 'is_active' => true, 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Tarjeta de Crédito', 'is_active' => true, 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Tarjeta de Débito', 'is_active' => true, 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Transferencia Bancaria', 'is_active' => true, 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Seguro Médico', 'is_active' => true, 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'QR / Pago Móvil', 'is_active' => true, 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Cheque', 'is_active' => true, 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Crédito Hospitalario', 'is_active' => true, 'created_at' => now()],
        ];

        DB::table('payment_methods')->insert($paymentMethods);
    }
}
