<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/api/v1/status', 'GET', ['page' => 1, 'per_page' => 10]);
$controller = $app->make(App\Http\Controllers\StatusController::class);
$response = $controller->index($request);
echo $response->getContent();
