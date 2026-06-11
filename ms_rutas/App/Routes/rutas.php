<?php
use App\Controllers\RutasController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function ($app) {
    $controller = new RutasController();

    $app->get('/api/rutas', function (Request $request, Response $response) use ($controller) {
        return $controller->index($request, $response);
    });

    $app->get('/api/rutas/{id}', function (Request $request, Response $response, $args) use ($controller) {
        return $controller->show($request, $response, $args);
    });

    $app->post('/api/rutas', function (Request $request, Response $response) use ($controller) {
        return $controller->create($request, $response);
    });

    $app->put('/api/rutas/{id}', function (Request $request, Response $response, $args) use ($controller) {
        return $controller->update($request, $response, $args);
    });

    $app->delete('/api/rutas/{id}', function (Request $request, Response $response, $args) use ($controller) {
        return $controller->delete($request, $response, $args);
    });

    $app->get('/api/rutas/health', function (Request $request, Response $response) use ($controller) {
        return $controller->health($request, $response);
    });
};
