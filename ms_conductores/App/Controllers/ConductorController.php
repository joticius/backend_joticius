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
        $data = $this->model->getAll();
        $response->getBody()->write(json_encode(['success' => true, 'data' => $data]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function get(Request $request, Response $response, $args)
    {
        $id = $args['id'] ?? null;
        $item = $this->model->findById($id);
        if (!$item) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Conductor no encontrado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        $response->getBody()->write(json_encode(['success' => true, 'data' => $item]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function create(Request $request, Response $response)
    {
        $data = json_decode($request->getBody(), true);
        if (!$data || empty($data['nombre']) || empty($data['licencia'])) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Datos inválidos']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $ok = $this->model->create($data);
        if ($ok) {
            $response->getBody()->write(json_encode(['success' => true, 'message' => 'Conductor creado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        }

        $response->getBody()->write(json_encode(['success' => false, 'message' => 'Error al crear conductor']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }

    public function update(Request $request, Response $response, $args)
    {
        $id = $args['id'] ?? null;
        $data = json_decode($request->getBody(), true);
        if (!$data) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Datos inválidos']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $ok = $this->model->update($id, $data);
        if ($ok) {
            $response->getBody()->write(json_encode(['success' => true, 'message' => 'Conductor actualizado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }

        $response->getBody()->write(json_encode(['success' => false, 'message' => 'Error al actualizar conductor']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }

    public function delete(Request $request, Response $response, $args)
    {
        $id = $args['id'] ?? null;
        $ok = $this->model->delete($id);
        if ($ok) {
            $response->getBody()->write(json_encode(['success' => true, 'message' => 'Conductor eliminado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }
        $response->getBody()->write(json_encode(['success' => false, 'message' => 'Error al eliminar conductor']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }

    public function health(Request $request, Response $response)
    {
        $response->getBody()->write(json_encode(['success' => true, 'message' => 'ms_conductores operativo', 'timestamp' => date('Y-m-d H:i:s')]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
