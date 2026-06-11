<?php
namespace App\Middleware;

use App\Config\Settings;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandler $handler): Response
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (empty($authHeader)) {
            return $this->unauthorized('Token no proporcionado');
        }

        $parts = explode(' ', $authHeader);
        if (count($parts) !== 2 || $parts[0] !== 'Bearer') {
            return $this->unauthorized('Formato de token inválido');
        }

        $token = $parts[1];

        try {
            $decoded = JWT::decode($token, new Key(Settings::getJwtSecret(), Settings::getJwtAlgorithm()));

            $request = $request
                ->withAttribute('usuario_id', $decoded->usuario_id ?? null)
                ->withAttribute('rol', $decoded->rol ?? null);

            return $handler->handle($request);
        } catch (\Firebase\JWT\ExpiredException $e) {
            return $this->unauthorized('Token expirado');
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return $this->unauthorized('Token inválido');
        } catch (\Exception $e) {
            return $this->unauthorized('Error al validar token: ' . $e->getMessage());
        }
    }

    private function unauthorized($message): Response
    {
        $response = new \Slim\Psr7\Response();
        $data = ['success' => false, 'message' => $message];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    }
}
