<?php
// index.php
session_start();

// Inclure les fichiers de configuration
require_once "config/database.php";

// Inclure les modèles
require_once "models/Game.php";
require_once "models/Player.php";
require_once "models/Stock.php";

// Inclure les contrôleurs
require_once "controllers/AuthController.php";
require_once "controllers/GameController.php";
require_once "controllers/PlayerController.php";
require_once "controllers/StockController.php";

// Routing simple
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// En-tête
include_once "includes/header.php";

// Routage principal
switch ($page) {
    case 'home':
        if (isset($_SESSION['player_id'])) {
            $gameController = new GameController();
            $gameController->dashboard();
        } else {
            include_once "views/auth/login.php";
        }
        break;
    
    case 'auth':
        $authController = new AuthController();
        
        switch ($action) {
            case 'login':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $authController->login($_POST['email'], $_POST['password']);
                } else {
                    include_once "views/auth/login.php";
                }
                break;
            
            case 'register':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $authController->register($_POST['email'], $_POST['username'], $_POST['password']);
                } else {
                    include_once "views/auth/register.php";
                }
                break;
            
            case 'logout':
                $authController->logout();
                break;
            
            default:
                include_once "views/auth/login.php";
                break;
        }
        break;
    
    case 'game':
        if (!isset($_SESSION['player_id'])) {
            header("Location: index.php?page=auth&action=login");
            exit;
        }
        
        $gameController = new GameController();
        
        switch ($action) {
            case 'dashboard':
                $gameController->dashboard();
                break;
            
            case 'next_turn':
                $gameController->nextTurn();
                break;
            
            case 'history':
                $gameController->history();
                break;
            
            default:
                $gameController->dashboard();
                break;
        }
        break;
    
    case 'stocks':
        if (!isset($_SESSION['player_id'])) {
            header("Location: index.php?page=auth&action=login");
            exit;
        }
        
        $stockController = new StockController();
        
        switch ($action) {
            case 'list':
                $stockController->listStocks();
                break;
            
            case 'details':
                $stockId = isset($_GET['id']) ? $_GET['id'] : null;
                $stockController->details($stockId);
                break;
            
            case 'buy':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $stockId = $_POST['stock_id'];
                    $quantity = $_POST['quantity'];
                    $stockController->buy($stockId, $quantity);
                } else {
                    header("Location: index.php?page=stocks&action=list");
                }
                break;
            
            case 'sell':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $stockId = $_POST['stock_id'];
                    $quantity = $_POST['quantity'];
                    $stockController->sell($stockId, $quantity);
                } else {
                    header("Location: index.php?page=stocks&action=list");
                }
                break;
            
            default:
                $stockController->listStocks();
                break;
        }
        break;
    
    case 'players':
        if (!isset($_SESSION['player_id'])) {
            header("Location: index.php?page=auth&action=login");
            exit;
        }
        
        $playerController = new PlayerController();
        
        switch ($action) {
            case 'profile':
                $playerId = isset($_GET['id']) ? $_GET['id'] : $_SESSION['player_id'];
                $playerController->profile($playerId);
                break;
            
            case 'ranking':
                $playerController->ranking();
                break;
            
            default:
                $playerController->profile($_SESSION['player_id']);
                break;
        }
        break;
    
    default:
        // Page 404
        echo "<div class='container mt-5'><h1>Page non trouvée</h1><p>La page demandée n'existe pas.</p></div>";
        break;
}

// Pied de page
include_once "includes/footer.php";
?>
