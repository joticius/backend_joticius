<?php

use App\Controllers\ViajesController;
use Slim\App;

return function (App $app) {
    $controller = new ViajesController();

    $app->get('/api/viajes/health', [$controller, 'health']);
    $app->get('/api/viajes', [$controller, 'index']);
    $app->get('/api/viajes/{id}', [$controller, 'show']);
    $app->post('/api/viajes', [$controller, 'create']);
    $app->put('/api/viajes/{id}', [$controller, 'update']);
    $app->delete('/api/viajes/{id}', [$controller, 'delete']);
};