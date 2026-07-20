<?php

namespace App\Core;

use PDO;

class Model
{
    protected $db;         // Instance de PDO
    protected $table;      // Nom de la table (doit être défini dans l'enfant)

    public function __construct()
    {
        // Récupère la connexion PDO via Database
        $this->db = Database::getInstance()->getConnection();

        // Si le nom de la table n'est pas défini dans l'enfant, on le déduit du nom de la classe
        if (!$this->table) {
            $className = get_class($this);
            $parts = explode('\\', $className);
            $baseName = end($parts);
            // Ex: UserModel -> 'users'
            $this->table = strtolower(str_replace('Model', '', $baseName)) . 's';
        }
    }

    // Récupère un enregistrement par son ID
    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupère tous les enregistrements
    public function findAll()
    {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Insère un nouvel enregistrement (ex: $data = ['name' => 'toto', 'age' => 25])
    public function insert($data)
    {
        $keys = array_keys($data);
        $fields = implode(', ', $keys);
        $placeholders = ':' . implode(', :', $keys);

        $sql = "INSERT INTO {$this->table} ({$fields}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        return $this->db->lastInsertId();
    }

    // Met à jour un enregistrement (ex: update(1, ['balance' => 1000]))
    public function update($id, $data)
    {
        $setParts = [];
        foreach ($data as $key => $value) {
            $setParts[] = "{$key} = :{$key}";
        }
        $setClause = implode(', ', $setParts);
        
        $data['id'] = $id; // Ajoute l'ID pour la clause WHERE
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    // Supprime un enregistrement
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Méthode utilitaire : Exécute une requête personnalisée avec des paramètres
    public function query($sql, $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}