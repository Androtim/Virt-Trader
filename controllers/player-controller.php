<?php
// controllers/PlayerController.php
class PlayerController {
    private $db;
    private $player;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->player = new Player($this->db);
    }
    
    public function profile($playerId) {
        // Vérifier si l'ID est valide
        if (!$playerId) {
            $_SESSION['error'] = "Joueur non spécifié.";
            header("Location: index.php?page=game&action=dashboard");
            exit;
        }
        
        // Récupérer les informations du joueur
        $this->player->id = $playerId;
        if ($this->player->readOne()) {
            // Récupérer le portefeuille du joueur
            $portfolio = $this->player->getPortfolio();
            $portfolioItems = [];
            $totalPortfolioValue = 0;
            
            while ($item = $portfolio->fetch(PDO::FETCH_ASSOC)) {
                $totalPortfolioValue += $item['total_value'];
                $portfolioItems[] = $item;
            }
            
            // Récupérer l'historique des transactions si c'est le joueur actuel
            $transactions = [];
            if ($playerId == $_SESSION['player_id']) {
                $transactionsResult = $this->player->getTransactionHistory();
                while ($transaction = $transactionsResult->fetch(PDO::FETCH_ASSOC)) {
                    $transactions[] = $transaction;
                }
            }
            
            // Inclure la vue du profil
            include_once "views/players/profile.php";
        } else {
            $_SESSION['error'] = "Joueur introuvable.";
            header("Location: index.php?page=game&action=dashboard");
            exit;
        }
    }
    
    public function ranking() {
        // Récupérer le classement des joueurs
        $rankingResult = $this->player->getRanking();
        $ranking = [];
        $rank = 1;
        
        while ($player = $rankingResult->fetch(PDO::FETCH_ASSOC)) {
            $player['rank'] = $rank++;
            $ranking[] = $player;
        }
        
        // Inclure la vue du classement
        include_once "views/players/ranking.php";
    }
}
?>
