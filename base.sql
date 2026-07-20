PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS operators (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE,
    is_own_operator INTEGER NOT NULL DEFAULT 0
        CHECK (is_own_operator IN (0, 1)),
    commission_percent REAL NOT NULL DEFAULT 0
        CHECK (commission_percent >= 0),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS prefixes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    operator_id INTEGER NOT NULL,
    prefix TEXT NOT NULL UNIQUE,
    FOREIGN KEY (operator_id) REFERENCES operators(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS operation_types (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT NOT NULL UNIQUE,
    name TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS fee_rules (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    operation_type_id INTEGER NOT NULL,
    min_amount INTEGER NOT NULL,
    max_amount INTEGER NOT NULL,
    fee INTEGER NOT NULL,
    FOREIGN KEY (operation_type_id) REFERENCES operation_types(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    phone_number TEXT NOT NULL UNIQUE,
    balance INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,

    user_id INTEGER NOT NULL,
    operation_type_id INTEGER NOT NULL,

    receiver_user_id INTEGER NULL,
    receiver_phone TEXT NULL,
    receiver_operator_id INTEGER NULL,

    amount INTEGER NOT NULL
        CHECK (amount > 0),

    base_fee INTEGER NOT NULL DEFAULT 0
        CHECK (base_fee >= 0),

    external_commission INTEGER NOT NULL DEFAULT 0
        CHECK (external_commission >= 0),

    included_withdrawal_fee INTEGER NOT NULL DEFAULT 0
        CHECK (included_withdrawal_fee >= 0),

    fee INTEGER NOT NULL DEFAULT 0
        CHECK (fee >= 0),

    total_fee INTEGER NOT NULL DEFAULT 0
        CHECK (total_fee >= 0),

    withdrawal_fee_included INTEGER NOT NULL DEFAULT 0
        CHECK (withdrawal_fee_included IN (0, 1)),

    is_external INTEGER NOT NULL DEFAULT 0
        CHECK (is_external IN (0, 1)),

    batch_reference TEXT NULL,

    status TEXT NOT NULL DEFAULT 'completed'
        CHECK (
            status IN (
                'pending',
                'completed',
                'failed',
                'cancelled'
            )
        ),

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (operation_type_id) REFERENCES operation_types(id),
    FOREIGN KEY (receiver_user_id) REFERENCES users(id),
    FOREIGN KEY (receiver_operator_id) REFERENCES operators(id)
);

INSERT INTO operators (name, is_own_operator, commission_percent) VALUES
('Notre operateur', 1, 0),
('Operateur 032', 0, 2),
('Operateur 031', 0, 2);

INSERT INTO prefixes (operator_id, prefix) VALUES
(1, '033'),
(1, '037'),
(2, '032'),
(3, '031');

INSERT INTO operation_types (code, name) VALUES
('depos', 'Depot'),
('retrait', 'Retrait'),
('transfer', 'Transfert');

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

INSERT INTO users (phone_number, balance) VALUES ('0331234567', 50000);
