<?php
use App\Controllers\AuthController;
use App\Middleware\AuthMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function ($app) {
    $authController = new AuthController();
    $authMiddleware = new AuthMiddleware();

    // PÚBLICAS
    $app->post('/api/auth/login', function (Request $request, Response $response) use ($authController) {
        return $authController->login($request, $response);
    });

    $app->post('/api/auth/register', function (Request $request, Response $response) use ($authController) {
        return $authController->register($request, $response);
    });

    $app->get('/api/auth/health', function (Request $request, Response $response) {
        $data = [
            'success' => true,
            'message' => 'ms_auth está operativo',
            'timestamp' => date('Y-m-d H:i:s')
        ];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    });

    // PROTEGIDAS
    $app->post('/api/auth/logout', function (Request $request, Response $response) use ($authController) {
        return $authController->logout($request, $response);
    })->add($authMiddleware);

    $app->get('/api/auth/profile', function (Request $request, Response $response) use ($authController) {
        return $authController->getProfile($request, $response);
    })->add($authMiddleware);
};