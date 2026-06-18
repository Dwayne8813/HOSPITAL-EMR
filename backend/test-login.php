<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/api/v1/auth/login', 'POST', [
    'email' => 'admin@hospital.bo',
    'password' => 'password'
], [], [], [
    'HTTP_CONTENT_TYPE' => 'application/json',
    'HTTP_ACCEPT' => 'application/json',
]);
$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
echo "Location: " . ($response->headers->get('Location') ?? 'none') . "\n";
echo "Content-Type: " . $response->headers->get('Content-Type') . "\n";
echo "Body: " . substr($response->getContent(), 0, 500) . "\n";
