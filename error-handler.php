<?php
// includes/error_handler.php

// Protection contre l'accès direct au fichier
if (!defined('ACCESS_GRANTED')) {
    header("HTTP/1.1 403 Forbidden");
    exit('Accès direct interdit');
}

// Mode débogage (à désactiver en production)
$debug_mode = false;

// Fonction pour journaliser les erreurs
function logError($message, $severity, $file, $line) {
    $log_file = __DIR__ . '/../logs/error.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] [$severity] $message in $file on line $line" . PHP_EOL;
    
    // Créer le répertoire de logs s'il n'existe pas
    if (!file_exists(dirname($log_file))) {
        mkdir(dirname($log_file), 0755, true);
    }
    
    // Écrire dans le fichier de log
    file_put_contents($log_file, $log_message, FILE_APPEND);
}

// Gestionnaire d'erreurs personnalisé
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    global $debug_mode;
    
    // Ne pas afficher les erreurs pour lesquelles @ a été utilisé
    if (error_reporting() === 0) {
        return true;
    }
    
    // Déterminer le type d'erreur
    switch ($errno) {
        case E_USER_ERROR:
            $severity = "ERREUR FATALE";
            break;
        case E_USER_WARNING:
        case E_WARNING:
            $severity = "AVERTISSEMENT";
            break;
        case E_USER_NOTICE:
        case E_NOTICE:
            $severity = "NOTICE";
            break;
        default:
            $severity = "ERREUR ($errno)";
            break;
    }
    
    // Journaliser l'erreur
    logError($errstr, $severity, $errfile, $errline);
    
    // Afficher l'erreur en mode débogage
    if ($debug_mode) {
        echo "<div style='background-color: #ffcccc; border: 1px solid #ff0000; padding: 10px; margin: 10px;'>";
        echo "<h3>$severity</h3>";
        echo "<p>$errstr</p>";
        echo "<p>Dans le fichier $errfile à la ligne $errline</p>";
        echo "</div>";
    }
    
    // Renvoyer true pour que PHP n'affiche pas l'erreur nativement
    return true;
}

// Gestionnaire d'exceptions
function customExceptionHandler($exception) {
    global $debug_mode;
    
    $message = $exception->getMessage();
    $file = $exception->getFile();
    $line = $exception->getLine();
    $trace = $exception->getTraceAsString();
    
    // Journaliser l'exception
    logError($message, "EXCEPTION", $file, $line);
    
    // Afficher l'exception en mode débogage
    if ($debug_mode) {
        echo "<div style='background-color: #ffcccc; border: 1px solid #ff0000; padding: 10px; margin: 10px;'>";
        echo "<h3>EXCEPTION</h3>";
        echo "<p>$message</p>";
        echo "<p>Dans le fichier $file à la ligne $line</p>";
        echo "<h4>Trace :</h4>";
        echo "<pre>$trace</pre>";
        echo "</div>";
    } else {
        // En production, afficher un message générique
        $_SESSION['error'] = "Une erreur est survenue. Veuillez réessayer plus tard.";
        header("Location: index.php");
        exit;
    }
}

// Gestionnaire d'erreurs fatales
function shutdownHandler() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        // Journaliser l'erreur fatale
        logError($error['message'], "ERREUR FATALE", $error['file'], $error['line']);
        
        // Rediriger vers une page d'erreur en production
        if (!$GLOBALS['debug_mode']) {
            header("HTTP/1.1 500 Internal Server Error");
            include __DIR__ . '/../views/error/500.php';
        }
    }
}

// Définir les gestionnaires
set_error_handler('customErrorHandler');
set_exception_handler('customExceptionHandler');
register_shutdown_function('shutdownHandler');
