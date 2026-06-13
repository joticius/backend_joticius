<?php
namespace App\Controllers;

use App\Models\Viaje;
use App\Config\Database;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ViajesController
{
    public function __construct()
    {
        Database::init();
    }

    public function health(Request $request, Response $response)
    {
        try {
            // Intentar obtener un viaje para verificar conexión a BD
            Viaje::query()->limit(1)->get();

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'ms_viajes operativo',
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

    public function index(Request $request, Response $response)
    {
        $query = Viaje::query();
        $params = $request->getQueryParams();

        if (!empty($params['conductor_id'])) {
            $query->where('conductor_id', $params['conductor_id']);
        }
        if (!empty($params['vehiculo_id'])) {
            $query->where('vehiculo_id', $params['vehiculo_id']);
        }
        if (!empty($params['estado'])) {
            $query->where('estado', $params['estado']);
        }
        if (!empty($params['fecha_salida'])) {
            $query->whereDate('fecha_salida', $params['fecha_salida']);
        }

        $data = $query->get();
        $response->getBody()->write(json_encode(['success' => true, 'data' => $data]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function show(Request $request, Response $response, $args)
    {
        $viaje = Viaje::find($args['id'] ?? null);
        if (!$viaje) {
            return $this->error($response, 'Viaje no encontrado', 404);
        }

        $response->getBody()->write(json_encode(['success' => true, 'data' => $viaje]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function create(Request $request, Response $response)
    {
        $data = json_decode($request->getBody(), true);
        if (!$data || empty($data['conductor_id']) || empty($data['vehiculo_id']) || empty($data['ruta_id']) || empty($data['fecha_salida']) || empty($data['hora_salida'])) {
            return $this->error($response, 'Datos inválidos', 400);
        }

        $viaje = Viaje::create([
            'conductor_id' => $data['conductor_id'],
            'vehiculo_id' => $data['vehiculo_id'],
            'ruta_id' => $data['ruta_id'],
            'fecha_salida' => $data['fecha_salida'],
            'hora_salida' => $data['hora_salida'],
            'fecha_estimada_llegada' => $data['fecha_estimada_llegada'] ?? null,
            'observaciones' => $data['observaciones'] ?? null,
            'estado' => $data['estado'] ?? 'programado'
        ]);

        $response->getBody()->write(json_encode(['success' => true, 'data' => $viaje]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function update(Request $request, Response $response, $args)
    {
        $viaje = Viaje::find($args['id'] ?? null);
        if (!$viaje) {
            return $this->error($response, 'Viaje no encontrado', 404);
        }

        $data = json_decode($request->getBody(), true);
        if (!$data) {
            return $this->error($response, 'Datos inválidos', 400);
        }

        $viaje->update($data);

        $response->getBody()->write(json_encode(['success' => true, 'data' => $viaje]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function delete(Request $request, Response $response, $args)
    {
        $viaje = Viaje::find($args['id'] ?? null);
        if (!$viaje) {
            return $this->error($response, 'Viaje no encontrado', 404);
        }

        $viaje->delete();
        $response->getBody()->write(json_encode(['success' => true, 'message' => 'Viaje eliminado']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    private function error(Response $response, $message, $status)
    {
        $response->getBody()->write(json_encode(['success' => false, 'message' => $message]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}