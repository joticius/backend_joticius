<?php
namespace App\Controllers;

use App\Models\Conductor;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ConductorController
{
    private $model;

    public function __construct()
    {
        $this->model = new Conductor();
    }

    public function index(Request $request, Response $response)
    {
        try {
            $data = $this->model->getAll();
            $response->getBody()->write(json_encode(['success' => true, 'data' => $data]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Error al obtener conductores: ' . $e->getMessage(), 500);
        }
    }

    public function get(Request $request, Response $response, $args)
    {
        try {
            $id = $args['id'] ?? null;
            if (!$id) {
                return $this->errorResponse($response, 'ID inválido', 400);
            }

            $item = $this->model->findById($id);
            if (!$item) {
                return $this->errorResponse($response, 'Conductor no encontrado', 404);
            }

            $response->getBody()->write(json_encode(['success' => true, 'data' => $item]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Error al obtener conductor: ' . $e->getMessage(), 500);
        }
    }

    public function create(Request $request, Response $response)
    {
        try {
            $data = json_decode($request->getBody(), true);

            // Validar datos requeridos (nombres, apellidos, documento, numero_licencia)
            if (!$data || empty($data['nombres']) || empty($data['apellidos']) || empty($data['documento']) || empty($data['numero_licencia'])) {
                return $this->errorResponse($response, 'Campos requeridos: nombres, apellidos, documento, numero_licencia', 400);
            }

            $ok = $this->model->create($data);
            if ($ok) {
                $response->getBody()->write(json_encode(['success' => true, 'message' => 'Conductor creado exitosamente']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
            }

            return $this->errorResponse($response, 'Error al crear conductor', 500);
        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Error en la creación: ' . $e->getMessage(), 500);
        }
    }

    public function update(Request $request, Response $response, $args)
    {
        try {
            $id = $args['id'] ?? null;
            if (!$id) {
                return $this->errorResponse($response, 'ID inválido', 400);
            }

            $data = json_decode($request->getBody(), true);
            if (!$data) {
                return $this->errorResponse($response, 'Datos inválidos', 400);
            }

            $ok = $this->model->update($id, $data);
            if ($ok) {
                $response->getBody()->write(json_encode(['success' => true, 'message' => 'Conductor actualizado exitosamente']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            }

            return $this->errorResponse($response, 'Error al actualizar conductor', 500);
        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Error en la actualización: ' . $e->getMessage(), 500);
        }
    }

    public function delete(Request $request, Response $response, $args)
    {
        try {
            $id = $args['id'] ?? null;
            if (!$id) {
                return $this->errorResponse($response, 'ID inválido', 400);
            }

            $ok = $this->model->delete($id);
            if ($ok) {
                $response->getBody()->write(json_encode(['success' => true, 'message' => 'Conductor eliminado exitosamente']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            }

            return $this->errorResponse($response, 'Error al eliminar conductor', 500);
        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Error en la eliminación: ' . $e->getMessage(), 500);
        }
    }

    public function health(Request $request, Response $response)
    {
        try {
            // Intentar obtener un conductor para verificar conexión a BD
            $this->model->getAll();

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'ms_conductores operativo',
                'timestamp' => date('Y-m-d H:i:s'),
                'db_status' => 'conectado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error del servidor',
                'error' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Helper para respuestas de error
     */
    private function errorResponse(Response $response, $message, $code)
    {
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => $message
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($code);
    }
}


