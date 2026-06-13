<?php

use App\Controllers\RutasController;
use Slim\App;

return function (App $app) {
    $controller = new RutasController();

    $app->get('/api/rutas/health', [$controller, 'health']);
    $app->get('/api/rutas', [$controller, 'index']);
    $app->get('/api/rutas/{id}', [$controller, 'show']);
    $app->post('/api/rutas', [$controller, 'create']);
    $app->put('/api/rutas/{id}', [$controller, 'update']);
    $app->delete('/api/rutas/{id}', [$controller, 'delete']);
};