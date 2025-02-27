<?php
// controllers/GameController.php
class GameController {
    private $db;
    private $game;
    private $player;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->game = new Game($this->db);
        $this->player = new Player($this->db);
    }
    
    public function dashboard() {
        // Lire l'état actuel du jeu
        $this->game->readCurrentState();
        
        // Récupérer les informations du joueur
        $this->player->id = $_SESSION['player_id'];
        $this->player->readOne();
        
        // Mettre à jour la session avec le solde actuel
        $_SESSION['balance'] = $this->player->balance;
        
        // Récupérer le portefeuille du joueur
        $portfolio = $this->player->getPortfolio();
        
        // Calculer la valeur totale du portefeuille
        $totalPortfolioValue = 0;
        $portfolioItems = [];
        
        while ($item = $portfolio->fetch(PDO::FETCH_ASSOC)) {
            $totalPortfolioValue += $item['total_value'];
            $portfolioItems[] = $item;
        }
        
        // Récupérer les dernières transactions
        $transactions = $this->player->getTransactionHistory();
        $recentTransactions = [];
        $count = 0;
        
        while ($transaction = $transactions->fetch(PDO::FETCH_ASSOC)) {
            if ($count < 5) {
                $recentTransactions[] = $transaction;
                $count++;
            } else {
                break;
            }
        }
        
        // Inclure la vue du tableau de bord
        include_once "views/game/dashboard.php";
    }
    
    public function nextTurn() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['player_id'])) {
            header("Location: index.php?page=auth&action=login");
            exit;
        }
        
        // Passer au mois suivant
        if ($this->game->nextMonth()) {
            // Mettre à jour les prix des actions
            $this->game->updateStockPrices();
            
            // Distribuer les dividendes
            $this->game->distributeDividends();
            
            // Vérifier les conditions de fin de jeu
            $this->game->checkGameOver();
            
            $_SESSION['success'] = "Tour suivant ! Nous sommes maintenant en " . date("F Y", mktime(0, 0, 0, $this->game->current_month, 1, $this->game->current_year));
        } else {
            $_SESSION['error'] = "Erreur lors du passage au tour suivant.";
        }
        
        // Rediriger vers le tableau de bord
        header("Location: index.php?page=game&action=dashboard");
        exit;
    }
    
    public function history() {
        // Lire l'état actuel du jeu
        $this->game->readCurrentState();
        
        // Récupérer les informations du joueur
        $this->player->id = $_SESSION['player_id'];
        
        // Récupérer l'historique complet des transactions
        $transactions = $this->player->getTransactionHistory();
        $allTransactions = [];
        
        while ($transaction = $transactions->fetch(PDO::FETCH_ASSOC)) {
            $allTransactions[] = $transaction;
        }
        
        // Inclure la vue de l'historique
        include_once "views/game/history.php";
    }
}
?>
