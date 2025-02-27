<?php
// config/config.php

// Protection contre l'accès direct au fichier
if (!defined('ACCESS_GRANTED')) {
    header("HTTP/1.1 403 Forbidden");
    exit('Accès direct interdit');
}

// Configuration de l'application
return [
    // Configuration de l'application
    'app' => [
        'name' => 'Virtual Trader',
        'description' => 'Simulateur de bourse virtuelle',
        'version' => '1.0.0',
        'debug' => false,
        'timezone' => 'Europe/Paris',
        'locale' => 'fr_FR',
    ],
    
    // Configuration du jeu
    'game' => [
        'initial_balance' => 10000.00,
        'min_balance' => 1000.00,
        'price_variation_min' => -10,
        'price_variation_max' => 10,
        'random_factor_min' => -3,
        'random_factor_max' => 3,
    ],
    
    // Configuration de la base de données
    'database' => [
        'host' => 'localhost',
        'name' => 'virtual_trader',
        'user' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],
    
    // Configuration de sécurité
    'security' => [
        'session_name' => 'virtual_trader_session',
        'password_algo' => PASSWORD_BCRYPT,
        'password_options' => [
            'cost' => 12,
        ],
        'csrf_protection' => true,
    ],
];
