<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Simulate request
$resp = $kernel->handle($request = Illuminate\Http\Request::create('/api/v1/patients', 'POST', [
    'first_name' => 'Test',
    'last_name' => 'Patient',
    'document_type' => 'CI',
    'document_number' => 'DBG999',
    'birth_date' => '1990-01-01',
    'gender' => 'M',
]));

echo "Status: " . $resp->getStatusCode() . "\n";
echo "Body: " . $resp->getContent() . "\n";
