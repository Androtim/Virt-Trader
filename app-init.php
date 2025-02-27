<?php
// init.php

// Définir la constante d'accès
define('ACCESS_GRANTED', true);

// Démarrer la session
session_start();

// Définir le fuseau horaire
date_default_timezone_set('Europe/Paris');

// Charger la configuration
$config = require_once 'config/config.php';

// Charger le gestionnaire d'erreurs
require_once 'includes/error_handler.php';

// Charger la classe utilitaire
require_once 'includes/utilities.php';

// Charger les vérifications d'authentification
require_once 'includes/auth_check.php';

// Charger la base de données
require_once 'config/database.php';

// Établir la connexion à la base de données
$database = new Database();
$db = $database->getConnection();

// Charger les modèles
require_once 'models/Game.php';
require_once 'models/Player.php';
require_once 'models/Stock.php';

// Charger les contrôleurs
require_once 'controllers/AuthController.php';
require_once 'controllers/GameController.php';
require_once 'controllers/PlayerController.php';
require_once 'controllers/StockController.php';

// Fonction pour rediriger avec un message
function redirect($location, $message = null, $type = 'success') {
    if ($message) {
        $_SESSION[($type === 'success' ? 'success' : 'error')] = $message;
    }
    header("Location: $location");
    exit;
}

// Fonction pour nettoyer l'URL
function cleanUrl($url) {
    return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
}
