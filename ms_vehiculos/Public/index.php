<?php
// ms_vehiculos public entrypoint
$dotenvPath = __DIR__ . '/../.env';
if (file_exists($dotenvPath)) {
    $dotenv = parse_ini_file($dotenvPath);
    foreach ($dotenv as $key => $value) {
        $_ENV[$key] = $value;
    }
}

require_once __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpMethodNotAllowedException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app = AppFactory::create();// CORS
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
});

$app->options('/{routes:.+}', function ($request, $response) {
    return $response;
});

$app->add(function (Request $request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
});

$app->options('/{routes:.+}', function (Request $request, Response $response) {
    return $response;
});

$app->add(function (Request $request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
        ->withHeader('Access-Control-Max-Age', '3600');
});

$app->options('/{routes:.+}', function (Request $request, Response $response) {
    return $response;
});

$errorMiddleware = $app->addErrorMiddleware(
    ($_ENV['APP_DEBUG'] ?? 'false') === 'true',
    true,
    true
);

$errorMiddleware->setDefaultErrorHandler(function (Request $request, Throwable $exception, bool $displayErrorDetails) {
    $response = new \Slim\Psr7\Response();
    $statusCode = 500;

    if ($exception instanceof HttpNotFoundException) {
        $statusCode = 404;
    } elseif ($exception instanceof HttpMethodNotAllowedException) {
        $statusCode = 405;
    }

    $data = [
        'success' => false,
        'message' => 'Error del servidor',
        'error' => $displayErrorDetails ? $exception->getMessage() : 'Error interno'
    ];

    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
});

$routes = require __DIR__ . '/../App/Routes/vehiculos.php';
$routes($app);

try {
    $app->run();
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fatal en aplicación',
        'error' => ($_ENV['APP_DEBUG'] ?? 'false') === 'true' ? $e->getMessage() : 'Error interno'
    ]);
    http_response_code(500);
}
