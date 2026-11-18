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
            ['institution_id' => $institutionId, 'name' => 'Cash', 'is_active' => true, 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Credit Card', 'is_active' => true, 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Debit Card', 'is_active' => true, 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Bank Transfer', 'is_active' => true, 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Medical Insurance', 'is_active' => true, 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'QR / Mobile Payment', 'is_active' => true, 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Check', 'is_active' => true, 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Hospital Credit', 'is_active' => true, 'created_at' => now()],
        ];

        DB::table('payment_methods')->insert($paymentMethods);
    }
}
