<?php
use App\Controllers\AuthController;
use App\Middleware\AuthMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function ($app) {
    $authController = new AuthController();
    $authMiddleware = new AuthMiddleware();

    // ============================================================
    // RUTAS PÚBLICAS (sin autenticación)
    // ============================================================

    /**
     * POST /api/auth/login
     * Login de usuario
     * Body: { "usuario": "string", "contrasena": "string" }
     */
    $app->post('/api/auth/login', function (Request $request, Response $response) use ($authController) {
        return $authController->login($request, $response);
    });

    /**
     * POST /api/auth/register
     * Registrar nuevo usuario
     * Body: { "nombre": "string", "correo": "string", "usuario": "string", "contrasena": "string", "rol": "string" }
     */
    $app->post('/api/auth/register', function (Request $request, Response $response) use ($authController) {
        return $authController->register($request, $response);
    });

    /**
     * GET /api/auth/health
     * Verificar que el servicio está activo
     */
    $app->get('/api/auth/health', function (Request $request, Response $response) {
        $data = [
            'success' => true,
            'message' => 'ms_auth está operativo',
            'timestamp' => date('Y-m-d H:i:s')
        ];

        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    });

    // ============================================================
    // RUTAS PROTEGIDAS (requieren autenticación)
    // ============================================================

    /**
     * POST /api/auth/logout
     * Logout de usuario (requiere autenticación)
     */
    $app->post('/api/auth/logout', function (Request $request, Response $response) use ($authController) {
        return $authController->logout($request, $response);
    })->add($authMiddleware);

    /**
     * GET /api/auth/profile
     * Obtener perfil del usuario autenticado (requiere autenticación)
     */
    $app->get('/api/auth/profile', function (Request $request, Response $response) use ($authController) {
        return $authController->getProfile($request, $response);
    })->add($authMiddleware);

    /**
     * GET /api/auth/verify-token
     * Verificar que un token es válido (requiere autenticación)
     */
    $app->get('/api/auth/verify-token', function (Request $request, Response $response) {
        $usuarioId = $request->getAttribute('usuario_id');
        $rol = $request->getAttribute('rol');

        $data = [
            'success' => true,
            'message' => 'Token válido',
            'usuario_id' => $usuarioId,
            'rol' => $rol
        ];

        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    })->add($authMiddleware);
};