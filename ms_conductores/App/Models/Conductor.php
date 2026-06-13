<?php
namespace App\Models;

use App\Config\Database;

class Conductor
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll()
    {
        $stmt = $this->db->prepare('SELECT id, nombres, apellidos, documento, telefono, correo, numero_licencia, categoria_licencia, fecha_vencimiento_licencia, estado, created_at FROM conductores');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare('SELECT id, nombres, apellidos, documento, telefono, correo, numero_licencia, categoria_licencia, fecha_vencimiento_licencia, estado, created_at FROM conductores WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare('INSERT INTO conductores (nombres, apellidos, documento, telefono, correo, numero_licencia, categoria_licencia, fecha_vencimiento_licencia, estado, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
        return $stmt->execute([
            $data['nombres'] ?? null,
            $data['apellidos'] ?? null,
            $data['documento'] ?? null,
            $data['telefono'] ?? null,
            $data['correo'] ?? null,
            $data['numero_licencia'] ?? null,
            $data['categoria_licencia'] ?? null,
            $data['fecha_vencimiento_licencia'] ?? null,
            $data['estado'] ?? 'disponible'
        ]);
    }

    public function update($id, $data)
    {
        $fields = [];
        $values = [];

        if (isset($data['nombres'])) { $fields[] = 'nombres = ?'; $values[] = $data['nombres']; }
        if (isset($data['apellidos'])) { $fields[] = 'apellidos = ?'; $values[] = $data['apellidos']; }
        if (isset($data['documento'])) { $fields[] = 'documento = ?'; $values[] = $data['documento']; }
        if (isset($data['telefono'])) { $fields[] = 'telefono = ?'; $values[] = $data['telefono']; }
        if (isset($data['correo'])) { $fields[] = 'correo = ?'; $values[] = $data['correo']; }
        if (isset($data['numero_licencia'])) { $fields[] = 'numero_licencia = ?'; $values[] = $data['numero_licencia']; }
        if (isset($data['categoria_licencia'])) { $fields[] = 'categoria_licencia = ?'; $values[] = $data['categoria_licencia']; }
        if (isset($data['fecha_vencimiento_licencia'])) { $fields[] = 'fecha_vencimiento_licencia = ?'; $values[] = $data['fecha_vencimiento_licencia']; }
        if (isset($data['estado'])) { $fields[] = 'estado = ?'; $values[] = $data['estado']; }

        if (empty($fields)) return false;

        $values[] = $id;
        $sql = 'UPDATE conductores SET ' . implode(', ', $fields) . ', updated_at = NOW() WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare('DELETE FROM conductores WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
