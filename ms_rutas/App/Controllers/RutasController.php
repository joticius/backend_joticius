<?php
namespace App\Controllers;

use App\Models\Ruta;
use App\Config\Database;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RutasController
{
    public function __construct()
    {
        Database::init();
    }

    public function health(Request $request, Response $response)
    {
        try {
            // Intentar obtener una ruta para verificar conexión a BD
            Ruta::query()->limit(1)->get();

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'ms_rutas operativo',
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
        $query = Ruta::query();
        $params = $request->getQueryParams();

        if (!empty($params['origen'])) {
            $query->where('origen', 'like', '%' . $params['origen'] . '%');
        }
        if (!empty($params['destino'])) {
            $query->where('destino', 'like', '%' . $params['destino'] . '%');
        }
        if (!empty($params['estado'])) {
            $query->where('estado', $params['estado']);
        }

        $data = $query->get();
        $response->getBody()->write(json_encode(['success' => true, 'data' => $data]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function show(Request $request, Response $response, $args)
    {
        $ruta = Ruta::find($args['id'] ?? null);
        if (!$ruta) {
            return $this->error($response, 'Ruta no encontrada', 404);
        }

        $response->getBody()->write(json_encode(['success' => true, 'data' => $ruta]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function create(Request $request, Response $response)
    {
        $data = json_decode($request->getBody(), true);
        if (!$data || empty($data['origen']) || empty($data['destino']) || empty($data['distancia']) || empty($data['tiempo_estimado'])) {
            return $this->error($response, 'Datos inválidos', 400);
        }

        if (floatval($data['distancia']) <= 0 || floatval($data['tiempo_estimado']) <= 0) {
            return $this->error($response, 'Distancia y tiempo deben ser mayores a cero', 400);
        }

        $ruta = Ruta::create([
            'origen' => $data['origen'],
            'destino' => $data['destino'],
            'distancia' => $data['distancia'],
            'tiempo_estimado' => $data['tiempo_estimado'],
            'observaciones' => $data['observaciones'] ?? null,
            'estado' => $data['estado'] ?? 'activa'
        ]);

        $response->getBody()->write(json_encode(['success' => true, 'data' => $ruta]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function update(Request $request, Response $response, $args)
    {
        $ruta = Ruta::find($args['id'] ?? null);
        if (!$ruta) {
            return $this->error($response, 'Ruta no encontrada', 404);
        }

        $data = json_decode($request->getBody(), true);
        if (!$data) {
            return $this->error($response, 'Datos inválidos', 400);
        }

        if (isset($data['distancia']) && floatval($data['distancia']) <= 0) {
            return $this->error($response, 'Distancia debe ser mayor a cero', 400);
        }

        if (isset($data['tiempo_estimado']) && floatval($data['tiempo_estimado']) <= 0) {
            return $this->error($response, 'Tiempo estimado debe ser mayor a cero', 400);
        }

        $ruta->update($data);

        $response->getBody()->write(json_encode(['success' => true, 'data' => $ruta]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function delete(Request $request, Response $response, $args)
    {
        $ruta = Ruta::find($args['id'] ?? null);
        if (!$ruta) {
            return $this->error($response, 'Ruta no encontrada', 404);
        }

        $ruta->delete();
        $response->getBody()->write(json_encode(['success' => true, 'message' => 'Ruta eliminada']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    private function error(Response $response, $message, $status)
    {
        $response->getBody()->write(json_encode(['success' => false, 'message' => $message]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}