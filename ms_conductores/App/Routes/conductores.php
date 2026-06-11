<?php
use App\Controllers\ConductorController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function ($app) {
	$controller = new ConductorController();

	// Public routes
	$app->get('/api/conductores', function (Request $request, Response $response) use ($controller) {
		return $controller->index($request, $response);
	});

	$app->get('/api/conductores/{id}', function (Request $request, Response $response, $args) use ($controller) {
		return $controller->get($request, $response, $args);
	});

	$app->post('/api/conductores', function (Request $request, Response $response) use ($controller) {
		return $controller->create($request, $response);
	});

	$app->put('/api/conductores/{id}', function (Request $request, Response $response, $args) use ($controller) {
		return $controller->update($request, $response, $args);
	});

	$app->delete('/api/conductores/{id}', function (Request $request, Response $response, $args) use ($controller) {
		return $controller->delete($request, $response, $args);
	});

	// Health
	$app->get('/api/conductores/health', function (Request $request, Response $response) use ($controller) {
		return $controller->health($request, $response);
	});
};

