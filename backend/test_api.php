<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::create('/', 'GET'));

echo "=== Testing Appointments ===\n";
try {
    $a = \App\Models\Appointment::with('patient', 'doctor', 'specialty')->first();
    echo "Appointment: " . ($a ? $a->id . ' - ' . $a->patient?->first_name : 'NONE') . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== Testing Suppliers ===\n";
try {
    $s = \App\Models\Supplier::first();
    echo "Supplier: " . ($s ? $s->id . ' - ' . $s->name : 'NONE') . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
