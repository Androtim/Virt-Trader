<?php
// includes/auth_check.php

// Protection contre l'accès direct au fichier
if (!defined('ACCESS_GRANTED')) {
    header("HTTP/1.1 403 Forbidden");
    exit('Accès direct interdit');
}

// Vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['player_id']);
}

// Rediriger si l'utilisateur n'est pas connecté
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['error'] = "Vous devez être connecté pour accéder à cette page.";
        header("Location: index.php?page=auth&action=login");
        exit;
    }
}

// Rediriger si l'utilisateur est déjà connecté
function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header("Location: index.php?page=game&action=dashboard");
        exit;
    }
}

// Protection contre les attaques CSRF
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Vérifier le jeton CSRF
function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Erreur de validation du formulaire. Veuillez réessayer.";
        header("Location: index.php");
        exit;
    }
}

// Nettoyer les données utilisateur
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
