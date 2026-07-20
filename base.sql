-- Activation des clés étrangères pour SQLite
PRAGMA foreign_keys = ON;

-- Table des préfixes téléphoniques autorisés
CREATE TABLE IF NOT EXISTS prefixes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    prefix TEXT NOT NULL UNIQUE
);

-- Table des types d'opérations (Dépôt, Retrait, Transfert)
CREATE TABLE IF NOT EXISTS operation_types (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT NOT NULL UNIQUE,  -- 'depos', 'retrait', 'transfer'
    name TEXT NOT NULL
);

-- Table des barèmes de frais (modifiable par l'admin)
CREATE TABLE IF NOT EXISTS fee_rules (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    operation_type_id INTEGER NOT NULL,
    min_amount INTEGER NOT NULL,
    max_amount INTEGER NOT NULL,
    fee INTEGER NOT NULL,
    FOREIGN KEY (operation_type_id) REFERENCES operation_types(id) ON DELETE CASCADE
);

-- Table des clients (utilisateurs)
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    phone_number TEXT NOT NULL UNIQUE,
    balance INTEGER DEFAULT 0
);

-- Table des transactions (historique complet)
CREATE TABLE IF NOT EXISTS transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    operation_type_id INTEGER NOT NULL,
    amount INTEGER NOT NULL,
    fee INTEGER DEFAULT 0,
    receiver_user_id INTEGER NULL,  -- NULL si dépôt/retrait, rempli si transfert
    status TEXT DEFAULT 'completed',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (operation_type_id) REFERENCES operation_types(id),
    FOREIGN KEY (receiver_user_id) REFERENCES users(id)
);

---------------------------------------------------------
-- INSERTIONS DES DONNÉES INITIALES
---------------------------------------------------------

-- 1. Insérer les 3 types d'opérations
INSERT INTO operation_types (code, name) VALUES 
('depos', 'Dépôt'),
('retrait ', 'Retrait'),
('transfer', 'Transfert');

-- 2. Insérer les barèmes de frais pour le RETRAIT (operation_type_id = 2)
INSERT INTO fee_rules (operation_type_id, min_amount, max_amount, fee) VALUES
(2, 100, 1000, 50),
(2, 1001, 5000, 50),
(2, 5001, 10000, 100),
(2, 10001, 25000, 200),
(2, 25001, 50000, 400),
(2, 50001, 100000, 800),
(2, 100001, 250000, 1500),
(2, 250001, 500000, 1500),
(2, 500001, 1000000, 2500),
(2, 1000001, 2000000, 3000);

-- 3. Insérer les barèmes de frais pour le TRANSFERT (operation_type_id = 3)
INSERT INTO fee_rules (operation_type_id, min_amount, max_amount, fee) VALUES
(3, 100, 1000, 50),
(3, 1001, 5000, 50),
(3, 5001, 10000, 100),
(3, 10001, 25000, 200),
(3, 25001, 50000, 400),
(3, 50001, 100000, 800),
(3, 100001, 250000, 1500),
(3, 250001, 500000, 1500),
(3, 500001, 1000000, 2500),
(3, 1000001, 2000000, 3000);

-- 4. Insérer les préfixes autorisés
INSERT INTO prefixes (prefix) VALUES ('033'), ('037');

-- 5. (Optionnel) Ajouter un client de test pour faciliter les tests
INSERT INTO users (phone_number, balance) VALUES ('0331234567', 50000);