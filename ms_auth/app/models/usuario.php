<?php
namespace App\Models;

use App\Config\Database;

class Usuario
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Buscar usuario por email
     */
    public function findByEmail($email)
    {
        $query = "SELECT * FROM usuarios WHERE correo = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /**
     * Buscar usuario por username
     */
    public function findByUsername($username)
    {
        $query = "SELECT * FROM usuarios WHERE usuario = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    /**
     * Buscar usuario por ID
     */
    public function findById($id)
    {
        $query = "SELECT * FROM usuarios WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Crear nuevo usuario
     */
    public function create($data)
    {
        $query = "INSERT INTO usuarios 
                  (nombre, correo, usuario, contrasena, rol, estado, created_at, updated_at)
                  VALUES (?, ?, ?, ?, ?, 'activo', NOW(), NOW())";
        
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([
            $data['nombre'],
            $data['correo'],
            $data['usuario'],
            password_hash($data['contrasena'], PASSWORD_BCRYPT),
            $data['rol']
        ]);
    }

    /**
     * Actualizar token de usuario
     */
    public function updateToken($id, $token)
    {
        $query = "UPDATE usuarios SET token = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$token, $id]);
    }

    /**
     * Actualizar sesión activa
     */
    public function updateSessionStatus($id, $status)
    {
        $query = "UPDATE usuarios SET sesion_activa = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$status ? 1 : 0, $id]);
    }

    /**
     * Obtener todos los usuarios (solo admin)
     */
    public function getAll()
    {
        $query = "SELECT id, nombre, correo, usuario, rol, estado, sesion_activa, created_at FROM usuarios";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Actualizar información de usuario
     */
    public function update($id, $data)
    {
        $fields = [];
        $values = [];

        if (isset($data['nombre'])) {
            $fields[] = "nombre = ?";
            $values[] = $data['nombre'];
        }

        if (isset($data['correo'])) {
            $fields[] = "correo = ?";
            $values[] = $data['correo'];
        }

        if (isset($data['rol'])) {
            $fields[] = "rol = ?";
            $values[] = $data['rol'];
        }

        if (isset($data['estado'])) {
            $fields[] = "estado = ?";
            $values[] = $data['estado'];
        }

        if (empty($fields)) {
            return false;
        }

        $fields[] = "updated_at = NOW()";
        $values[] = $id;

        $query = "UPDATE usuarios SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute($values);
    }

    /**
     * Eliminar usuario
     */
    public function delete($id)
    {
        $query = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id]);
    }

    /**
     * Verificar contraseña
     */
    public static function verifyPassword($inputPassword, $hashedPassword)
    {
        return password_verify($inputPassword, $hashedPassword);
    }
}