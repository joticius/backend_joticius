<?php

use App\Controllers\VehiculosController;
use Slim\App;

return function (App $app) {
    $controller = new VehiculosController();

    $app->get('/api/vehiculos/health', [$controller, 'health']);
    $app->get('/api/vehiculos', [$controller, 'index']);
    $app->get('/api/vehiculos/{id}', [$controller, 'show']);
    $app->post('/api/vehiculos', [$controller, 'create']);
    $app->put('/api/vehiculos/{id}', [$controller, 'update']);
    $app->delete('/api/vehiculos/{id}', [$controller, 'delete']);
};