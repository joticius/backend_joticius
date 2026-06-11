<?php

use App\Controllers\ViajeController;
use Slim\App;

return function (App $app) {
    $controller = new ViajeController();

    $app->get('/', [$controller, 'health']);
    $app->get('/viajes', [$controller, 'index']);
    $app->get('/viajes/{id}', [$controller, 'show']);
    $app->post('/viajes', [$controller, 'create']);
    $app->put('/viajes/{id}', [$controller, 'update']);
    $app->delete('/viajes/{id}', [$controller, 'delete']);
};
