<?php

use App\Controllers\VehiculosController;
use Slim\App;

return function (App $app) {
    $controller = new VehiculosController();

    $app->get('/', [$controller, 'health']);
    $app->get('/vehiculos', [$controller, 'index']);
    $app->get('/vehiculos/{id}', [$controller, 'show']);
    $app->post('/vehiculos', [$controller, 'create']);
    $app->put('/vehiculos/{id}', [$controller, 'update']);
    $app->delete('/vehiculos/{id}', [$controller, 'delete']);
};
