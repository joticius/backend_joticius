<?php
namespace App\Controllers;

use App\Config\Database;
use App\Models\Viajes;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ViajeController
{
    public function __construct()
    {
        Database::init();
    }

    public function index(Request $request, Response $response)
    {
        $query = Viajes::query();
        $params = $request->getQueryParams();

        if (!empty($params['programacion_viaje_id'])) {
            $query->where('programacion_viaje_id', $params['programacion_viaje_id']);
        }
        if (!empty($params['estado'])) {
            $query->where('estado', $params['estado']);
        }
        if (!empty($params['fecha'])) {
            $query->where('fecha', $params['fecha']);
        }
        if (!empty($params['novedad'])) {
            $query->where('novedad', 'like', '%' . $params['novedad'] . '%');
        }

        $data = $query->get();
        $response->getBody()->write(json_encode(['success' => true, 'data' => $data]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function show(Request $request, Response $response, $args)
    {
        $viaje = Viajes::find($args['id'] ?? null);
        if (!$viaje) {
            return $this->error($response, 'Registro de viaje no encontrado', 404);
        }

        $response->getBody()->write(json_encode(['success' => true, 'data' => $viaje]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function create(Request $request, Response $response)
    {
        $data = json_decode($request->getBody(), true);
        if (!$data || empty($data['programacion_viaje_id']) || empty($data['fecha']) || empty($data['hora']) || empty($data['estado'])) {
            return $this->error($response, 'Datos inválidos o incompletos', 400);
        }

        $viaje = Viajes::create([
            'programacion_viaje_id' => $data['programacion_viaje_id'],
            'fecha' => $data['fecha'],
            'hora' => $data['hora'],
            'estado' => $data['estado'],
            'novedad' => $data['novedad'] ?? null,
        ]);

        $response->getBody()->write(json_encode(['success' => true, 'data' => $viaje]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function update(Request $request, Response $response, $args)
    {
        $viaje = Viajes::find($args['id'] ?? null);
        if (!$viaje) {
            return $this->error($response, 'Registro de viaje no encontrado', 404);
        }

        $data = json_decode($request->getBody(), true);
        if (!$data) {
            return $this->error($response, 'Datos inválidos', 400);
        }

        if (isset($data['programacion_viaje_id']) && empty($data['programacion_viaje_id'])) {
            return $this->error($response, 'programacion_viaje_id es requerido', 400);
        }

        $viaje->fill($data);
        $viaje->save();

        $response->getBody()->write(json_encode(['success' => true, 'data' => $viaje]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function delete(Request $request, Response $response, $args)
    {
        $viaje = Viajes::find($args['id'] ?? null);
        if (!$viaje) {
            return $this->error($response, 'Registro de viaje no encontrado', 404);
        }

        $viaje->delete();
        $response->getBody()->write(json_encode(['success' => true, 'message' => 'Registro eliminado']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function health(Request $request, Response $response)
    {
        $response->getBody()->write(json_encode(['success' => true, 'message' => 'ms_viajes operativo', 'timestamp' => date('Y-m-d H:i:s')]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    private function error(Response $response, $message, $status)
    {
        $response->getBody()->write(json_encode(['success' => false, 'message' => $message]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}
