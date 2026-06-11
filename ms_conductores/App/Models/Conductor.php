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
        $stmt = $this->db->prepare('SELECT id, nombre, licencia, telefono, estado, created_at FROM conductores');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare('SELECT id, nombre, licencia, telefono, estado, created_at FROM conductores WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare('INSERT INTO conductores (nombre, licencia, telefono, estado, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())');
        return $stmt->execute([
            $data['nombre'] ?? null,
            $data['licencia'] ?? null,
            $data['telefono'] ?? null,
            $data['estado'] ?? 'activo'
        ]);
    }

    public function update($id, $data)
    {
        $fields = [];
        $values = [];

        if (isset($data['nombre'])) { $fields[] = 'nombre = ?'; $values[] = $data['nombre']; }
        if (isset($data['licencia'])) { $fields[] = 'licencia = ?'; $values[] = $data['licencia']; }
        if (isset($data['telefono'])) { $fields[] = 'telefono = ?'; $values[] = $data['telefono']; }
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
