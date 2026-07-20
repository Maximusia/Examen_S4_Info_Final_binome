<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $connection;

    // Constructeur privé : empêche d'instancier plusieurs fois
    private function __construct()
    {
        // Définit le chemin du fichier SQLite (dans writable/)
        $dbFile = WRITEPATH . 'database.db';

        try {
            // Connexion PDO à SQLite
            $this->connection = new PDO('sqlite:' . $dbFile);
            
            // Active le mode exception pour les erreurs SQL
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Récupère les résultats en tableau associatif par défaut
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Active les clés étrangères (indispensable pour tes relations)
            $this->connection->exec('PRAGMA foreign_keys = ON;');

        } catch (PDOException $e) {
            // En cas d'erreur, on arrête tout et on affiche le message
            die('Erreur de connexion à la base de données : ' . $e->getMessage());
        }
    }

    // Méthode statique pour récupérer l'unique instance
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Retourne l'objet PDO (pour exécuter des requêtes)
    public function getConnection()
    {
        return $this->connection;
    }
}