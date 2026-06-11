<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function ($app) {
    // GET /api/auth/login
    $app->post('/api/auth/login', function (Request $request, Response $response) {
        // Lógica aquí o llamar a controller
        $response->getBody()->write(json_encode(['status' => 'ok']));
        return $response->withHeader('Content-Type', 'application/json');
    });

    // POST /api/auth/register
    $app->post('/api/auth/register', function (Request $request, Response $response) {
        // Lógica
        return $response;
    });
};