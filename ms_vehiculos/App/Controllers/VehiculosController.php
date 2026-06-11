<?php
namespace App\Controllers;

use App\Models\Vehiculo;
use App\Config\Database;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class VehiculosController
{
    public function __construct()
    {
        Database::init();
    }

    public function index(Request $request, Response $response)
    {
        $query = Vehiculo::query();
        $params = $request->getQueryParams();

        if (!empty($params['placa'])) {
            $query->where('placa', 'like', '%' . $params['placa'] . '%');
        }
        if (!empty($params['estado'])) {
            $query->where('estado', $params['estado']);
        }
        if (!empty($params['tipo_vehiculo'])) {
            $query->where('tipo_vehiculo', 'like', '%' . $params['tipo_vehiculo'] . '%');
        }

        $data = $query->get();
        $response->getBody()->write(json_encode(['success' => true, 'data' => $data]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function show(Request $request, Response $response, $args)
    {
        $vehiculo = Vehiculo::find($args['id'] ?? null);
        if (!$vehiculo) {
            return $this->error($response, 'Vehículo no encontrado', 404);
        }

        $response->getBody()->write(json_encode(['success' => true, 'data' => $vehiculo]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function create(Request $request, Response $response)
    {
        $data = json_decode($request->getBody(), true);
        if (!$data || empty($data['placa']) || empty($data['tipo_vehiculo']) || empty($data['capacidad_carga']) || empty($data['modelo']) || empty($data['marca'])) {
            return $this->error($response, 'Datos inválidos', 400);
        }

        if (floatval($data['capacidad_carga']) <= 0) {
            return $this->error($response, 'La capacidad debe ser mayor a cero', 400);
        }

        if (Vehiculo::where('placa', $data['placa'])->exists()) {
            return $this->error($response, 'Placa duplicada', 409);
        }

        $vehiculo = Vehiculo::create([
            'placa' => $data['placa'],
            'tipo_vehiculo' => $data['tipo_vehiculo'],
            'capacidad_carga' => $data['capacidad_carga'],
            'modelo' => $data['modelo'],
            'marca' => $data['marca'],
            'estado' => $data['estado'] ?? 'disponible'
        ]);

        $response->getBody()->write(json_encode(['success' => true, 'data' => $vehiculo]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function update(Request $request, Response $response, $args)
    {
        $vehiculo = Vehiculo::find($args['id'] ?? null);
        if (!$vehiculo) {
            return $this->error($response, 'Vehículo no encontrado', 404);
        }

        $data = json_decode($request->getBody(), true);
        if (!$data) {
            return $this->error($response, 'Datos inválidos', 400);
        }

        if (isset($data['capacidad_carga']) && floatval($data['capacidad_carga']) <= 0) {
            return $this->error($response, 'La capacidad debe ser mayor a cero', 400);
        }

        if (isset($data['placa']) && $data['placa'] !== $vehiculo->placa) {
            if (Vehiculo::where('placa', $data['placa'])->exists()) {
                return $this->error($response, 'Placa duplicada', 409);
            }
        }

        $vehiculo->fill($data);
        $vehiculo->save();

        $response->getBody()->write(json_encode(['success' => true, 'data' => $vehiculo]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function delete(Request $request, Response $response, $args)
    {
        $vehiculo = Vehiculo::find($args['id'] ?? null);
        if (!$vehiculo) {
            return $this->error($response, 'Vehículo no encontrado', 404);
        }

        $vehiculo->delete();
        $response->getBody()->write(json_encode(['success' => true, 'message' => 'Vehículo eliminado']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function health(Request $request, Response $response)
    {
        $response->getBody()->write(json_encode(['success' => true, 'message' => 'ms_vehiculos operativo', 'timestamp' => date('Y-m-d H:i:s')]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    private function error(Response $response, $message, $status)
    {
        $response->getBody()->write(json_encode(['success' => false, 'message' => $message]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}
