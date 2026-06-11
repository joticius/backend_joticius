<?php
namespace App\Controllers;

use App\Models\Rutas;
use App\Config\Database;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RutasController
{
    public function __construct()
    {
        Database::init();
    }

    public function index(Request $request, Response $response)
    {
        $query = Rutas::query();

        // filters: ciudad, distancia, etc.
        $ciudad = $request->getQueryParams()['ciudad'] ?? null;
        if ($ciudad) {
            $query->where('ciudad_origen', 'like', "%$ciudad%")->orWhere('ciudad_destino', 'like', "%$ciudad%");
        }

        $data = $query->get();
        $response->getBody()->write(json_encode(['success' => true, 'data' => $data]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function show(Request $request, Response $response, $args)
    {
        $id = $args['id'] ?? null;
        $ruta = Rutas::find($id);
        if (!$ruta) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Ruta no encontrada']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        $response->getBody()->write(json_encode(['success' => true, 'data' => $ruta]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function create(Request $request, Response $response)
    {
        $data = json_decode($request->getBody(), true);
        if (!$data) {
            return $this->error($response, 'Datos inválidos', 400);
        }

        // validations
        if (empty($data['ciudad_origen']) || empty($data['ciudad_destino'])) {
            return $this->error($response, 'Ciudades origen y destino son requeridas', 400);
        }

        if (!isset($data['distancia']) || floatval($data['distancia']) <= 0) {
            return $this->error($response, 'Distancia debe ser mayor a cero', 400);
        }

        // avoid duplicate route (same origin+destination)
        $exists = Rutas::where('ciudad_origen', $data['ciudad_origen'])->where('ciudad_destino', $data['ciudad_destino'])->first();
        if ($exists) {
            return $this->error($response, 'Ruta duplicada', 409);
        }

        $ruta = Rutas::create($data);
        $response->getBody()->write(json_encode(['success' => true, 'data' => $ruta]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function update(Request $request, Response $response, $args)
    {
        $id = $args['id'] ?? null;
        $ruta = Rutas::find($id);
        if (!$ruta) return $this->error($response, 'Ruta no encontrada', 404);

        $data = json_decode($request->getBody(), true);
        if (!$data) return $this->error($response, 'Datos inválidos', 400);

        if (isset($data['distancia']) && floatval($data['distancia']) <= 0) {
            return $this->error($response, 'Distancia debe ser mayor a cero', 400);
        }

        $ruta->fill($data);
        $ruta->save();

        $response->getBody()->write(json_encode(['success' => true, 'data' => $ruta]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function delete(Request $request, Response $response, $args)
    {
        $id = $args['id'] ?? null;
        $ruta = Rutas::find($id);
        if (!$ruta) return $this->error($response, 'Ruta no encontrada', 404);

        $ruta->delete();
        $response->getBody()->write(json_encode(['success' => true, 'message' => 'Ruta eliminada']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function health(Request $request, Response $response)
    {
        $response->getBody()->write(json_encode(['success' => true, 'message' => 'ms_rutas operativo', 'timestamp' => date('Y-m-d H:i:s')]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    private function error(Response $response, $message, $code)
    {
        $response->getBody()->write(json_encode(['success' => false, 'message' => $message]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($code);
    }
}
