-- Création de la base de données
CREATE DATABASE IF NOT EXISTS virtual_trader;
USE virtual_trader;

-- Table des utilisateurs/joueurs
CREATE TABLE IF NOT EXISTS players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    username VARCHAR(50) NOT NULL,
    balance DECIMAL(10, 2) DEFAULT 10000.00,
    registration_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME DEFAULT NULL
);

-- Table des actions
CREATE TABLE IF NOT EXISTS stocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(10) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    initial_price DECIMAL(10, 2) NOT NULL,
    current_price DECIMAL(10, 2) NOT NULL,
    dividend_amount DECIMAL(10, 2) DEFAULT 0.00,
    dividend_month TINYINT DEFAULT NULL, -- Mois de distribution du dividende (1-12)
    last_update DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table des portefeuilles (actions possédées par les joueurs)
CREATE TABLE IF NOT EXISTS portfolios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id INT NOT NULL,
    stock_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    purchase_price DECIMAL(10, 2) NOT NULL,
    purchase_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    FOREIGN KEY (stock_id) REFERENCES stocks(id) ON DELETE CASCADE,
    UNIQUE KEY (player_id, stock_id)
);

-- Table des transactions
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id INT NOT NULL,
    stock_id INT NOT NULL,
    type ENUM('buy', 'sell') NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    transaction_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    FOREIGN KEY (stock_id) REFERENCES stocks(id) ON DELETE CASCADE
);

-- Table de l'historique des prix
CREATE TABLE IF NOT EXISTS price_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stock_id INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    game_month INT NOT NULL,
    game_year INT NOT NULL,
    real_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (stock_id) REFERENCES stocks(id) ON DELETE CASCADE
);

-- Table de l'état du jeu
CREATE TABLE IF NOT EXISTS game_state (
    id INT AUTO_INCREMENT PRIMARY KEY,
    current_month INT NOT NULL DEFAULT 1,
    current_year INT NOT NULL DEFAULT 1,
    last_update DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Insertion des données initiales pour l'état du jeu
INSERT INTO game_state (current_month, current_year) VALUES (1, 1);

-- Insertion de quelques actions fictives
INSERT INTO stocks (code, name, description, initial_price, current_price, dividend_amount, dividend_month) VALUES
('AAPL', 'Apple Inc.', 'Entreprise technologique spécialisée dans l''électronique grand public', 150.00, 150.00, 0.82, 2),
('GOOGL', 'Alphabet Inc.', 'Entreprise spécialisée dans les services et produits liés à Internet', 2800.00, 2800.00, 0.00, NULL),
('AMZN', 'Amazon.com Inc.', 'Entreprise de commerce électronique et de services cloud', 3200.00, 3200.00, 0.00, NULL),
('MSFT', 'Microsoft Corporation', 'Entreprise informatique multinationale', 280.00, 280.00, 0.56, 3),
('TSLA', 'Tesla, Inc.', 'Constructeur automobile de véhicules électriques', 700.00, 700.00, 0.00, NULL),
('FB', 'Meta Platforms, Inc.', 'Entreprise propriétaire de Facebook, Instagram et WhatsApp', 330.00, 330.00, 0.00, NULL),
('NFLX', 'Netflix, Inc.', 'Service de streaming vidéo', 530.00, 530.00, 0.00, NULL),
('DIS', 'The Walt Disney Company', 'Entreprise de médias et de divertissement', 175.00, 175.00, 0.88, 7),
('NVDA', 'NVIDIA Corporation', 'Concepteur de processeurs graphiques', 220.00, 220.00, 0.16, 3),
('JPM', 'JPMorgan Chase & Co.', 'Banque d''investissement et services financiers', 155.00, 155.00, 1.00, 1);
