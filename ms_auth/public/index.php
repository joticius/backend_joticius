<?php
/**
 * ms_auth - Microservicio de Autenticación
 * CargamEsta - Sistema de Transporte de Carga
 * 
 * Punto de entrada principal
 * Ejecutar con: php -S localhost:8000 -t public/
 */

// ============================================================
// CARGAR VARIABLES DE ENTORNO
// ============================================================
$dotenvPath = __DIR__ . '/../.env';
if (file_exists($dotenvPath)) {
    $dotenv = parse_ini_file($dotenvPath);
    foreach ($dotenv as $key => $value) {
        $_ENV[$key] = $value;
    }
}

// ============================================================
// CARGAR AUTOLOADER
// ============================================================
require_once __DIR__ . '/../vendor/autoload.php';

// ============================================================
// CREAR APLICACIÓN SLIM
// ============================================================
use Slim\Factory\AppFactory;
use Slim\Middleware\ErrorMiddleware;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpMethodNotAllowedException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app = AppFactory::create();

// ============================================================
// MIDDLEWARE GLOBAL
// ============================================================

// ============ CORS AQUÍ ============
$app->add(function (Request $request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
        ->withHeader('Access-Control-Max-Age', '3600');
});

// PREFLIGHT OPTIONS - AQUÍ VA EL FIX
$app->options('/{routes:.+}', function (Request $request, Response $response) {
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
        ->withStatus(200);
});

// Manejar preflight OPTIONS
$app->options('/{routes:.+}', function (Request $request, Response $response) {
    return $response;
});

// Error handling middleware
$errorMiddleware = $app->addErrorMiddleware(
    $_ENV['APP_DEBUG'] === 'true',
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
        'error' => $displayErrorDetails ? $exception->getMessage() : 'Erro interno'
    ];

    $response->getBody()->write(json_encode($data));
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus($statusCode);
});

// ============================================================
// CARGAR RUTAS
// ============================================================
$routes = require __DIR__ . '/../routes/route_auth.php';
$routes($app);

// ============================================================
// EJECUTAR APLICACIÓN
// ============================================================
try {
    $app->run();
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fatal en aplicación',
        'error' => $_ENV['APP_DEBUG'] === 'true' ? $e->getMessage() : 'Error interno'
    ]);
    http_response_code(500);
}