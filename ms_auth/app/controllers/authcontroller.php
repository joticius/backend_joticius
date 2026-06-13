<?php
namespace App\Controllers;

use App\Models\Usuario;
use App\Config\Settings;
use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }

    /**
     * Login - POST /api/auth/login
     */
    public function login(Request $request, Response $response)
    {
        try {
            $data = json_decode($request->getBody(), true);

            if (!isset($data['usuario']) || !isset($data['contrasena'])) {
                return $this->errorResponse(
                    $response,
                    'Usuario y contraseña son requeridos',
                    400
                );
            }

            // Buscar usuario
            $usuario = $this->usuarioModel->findByUsername($data['usuario']);

            if (!$usuario) {
                return $this->errorResponse(
                    $response,
                    'Usuario no encontrado',
                    401
                );
            }

            // Verificar contraseña (compatible con hashes Y texto plano para admin)
$passwordValid = password_verify($data['contrasena'], $usuario['contrasena']) || 
                 $data['contrasena'] === $usuario['contrasena'];

if (!$passwordValid) {
    return $this->errorResponse(
        $response,
        'Contraseña incorrecta',
        401
    );
}

            // Verificar estado
            if ($usuario['estado'] !== 'activo') {
                return $this->errorResponse(
                    $response,
                    'Usuario inactivo',
                    403
                );
            }

            // Generar JWT
            $token = $this->generateJWT($usuario['id'], $usuario['rol']);

            // Actualizar token y sesión
            $this->usuarioModel->updateToken($usuario['id'], $token);
            $this->usuarioModel->updateSessionStatus($usuario['id'], true);

            $responseData = [
                'success' => true,
                'message' => 'Login exitoso',
                'data' => [
                    'id' => $usuario['id'],
                    'nombre' => $usuario['nombre'],
                    'email' => $usuario['correo'],
                    'usuario' => $usuario['usuario'],
                    'rol' => $usuario['rol'],
                    'token' => $token
                ]
            ];

            $response->getBody()->write(json_encode($responseData));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);

        } catch (\Exception $e) {
            return $this->errorResponse(
                $response,
                'Error en login: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Register - POST /api/auth/register
     */
    public function register(Request $request, Response $response)
    {
        try {
            $data = json_decode($request->getBody(), true);

            // Validar datos requeridos
            $required = ['nombre', 'correo', 'usuario', 'contrasena'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    return $this->errorResponse(
                        $response,
                        "El campo '$field' es requerido",
                        400
                    );
                }
            }

            // Validar email
            if (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
                return $this->errorResponse(
                    $response,
                    'Email inválido',
                    400
                );
            }

            // Validar usuario único
            if ($this->usuarioModel->findByUsername($data['usuario'])) {
                return $this->errorResponse(
                    $response,
                    'Usuario ya existe',
                    409
                );
            }

            // Validar email único
            if ($this->usuarioModel->findByEmail($data['correo'])) {
                return $this->errorResponse(
                    $response,
                    'Email ya registrado',
                    409
                );
            }

            // Validar rol
            $rol = $data['rol'] ?? 'operador';
            if (!in_array($rol, Settings::getRoles())) {
                return $this->errorResponse(
                    $response,
                    'Rol inválido',
                    400
                );
            }

            // Validar contraseña
            if (strlen($data['contrasena']) < 6) {
                return $this->errorResponse(
                    $response,
                    'La contraseña debe tener al menos 6 caracteres',
                    400
                );
            }

            // Crear usuario
            $usuarioData = [
                'nombre' => $data['nombre'],
                'correo' => $data['correo'],
                'usuario' => $data['usuario'],
                'contrasena' => $data['contrasena'],
                'rol' => $rol
            ];

            if ($this->usuarioModel->create($usuarioData)) {
                $nuevoUsuario = $this->usuarioModel->findByUsername($data['usuario']);

                $responseData = [
                    'success' => true,
                    'message' => 'Usuario registrado exitosamente',
                    'data' => [
                        'id' => $nuevoUsuario['id'],
                        'nombre' => $nuevoUsuario['nombre'],
                        'email' => $nuevoUsuario['correo'],
                        'usuario' => $nuevoUsuario['usuario'],
                        'rol' => $nuevoUsuario['rol']
                    ]
                ];

                $response->getBody()->write(json_encode($responseData));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(201);
            } else {
                return $this->errorResponse(
                    $response,
                    'Error al registrar usuario',
                    500
                );
            }

        } catch (\Exception $e) {
            return $this->errorResponse(
                $response,
                'Error en registro: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Logout - POST /api/auth/logout
     */
    public function logout(Request $request, Response $response)
    {
        try {
            $usuarioId = $request->getAttribute('usuario_id');

            $this->usuarioModel->updateToken($usuarioId, null);
            $this->usuarioModel->updateSessionStatus($usuarioId, false);

            $responseData = [
                'success' => true,
                'message' => 'Logout exitoso'
            ];

            $response->getBody()->write(json_encode($responseData));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);

        } catch (\Exception $e) {
            return $this->errorResponse(
                $response,
                'Error en logout: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Obtener perfil del usuario autenticado - GET /api/auth/profile
     */
    public function getProfile(Request $request, Response $response)
    {
        try {
            $usuarioId = $request->getAttribute('usuario_id');
            $usuario = $this->usuarioModel->findById($usuarioId);

            if (!$usuario) {
                return $this->errorResponse(
                    $response,
                    'Usuario no encontrado',
                    404
                );
            }

            $responseData = [
                'success' => true,
                'data' => [
                    'id' => $usuario['id'],
                    'nombre' => $usuario['nombre'],
                    'email' => $usuario['correo'],
                    'usuario' => $usuario['usuario'],
                    'rol' => $usuario['rol'],
                    'estado' => $usuario['estado'],
                    'sesion_activa' => (bool) $usuario['sesion_activa'],
                    'created_at' => $usuario['created_at']
                ]
            ];

            $response->getBody()->write(json_encode($responseData));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);

        } catch (\Exception $e) {
            return $this->errorResponse(
                $response,
                'Error al obtener perfil: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Generar JWT
     */
    private function generateJWT($usuarioId, $rol)
    {
        $issuedAt = time();
        $expire = $issuedAt + Settings::getJwtExpiration();

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expire,
            'usuario_id' => $usuarioId,
            'rol' => $rol
        ];

        return JWT::encode(
            $payload,
            Settings::getJwtSecret(),
            Settings::getJwtAlgorithm()
        );
    }

    /**
     * Respuesta de error
     */
    private function errorResponse(Response $response, $message, $code)
    {
        $data = [
            'success' => false,
            'message' => $message
        ];

        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($code);
    }
}