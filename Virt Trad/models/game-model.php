<?php
// models/Game.php
class Game {
    private $conn;
    private $table_name = "game_state";
    
    public $id;
    public $current_month;
    public $current_year;
    public $last_update;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Lire l'état actuel du jeu
    public function readCurrentState() {
        $query = "SELECT id, current_month, current_year, last_update 
                FROM " . $this->table_name . " 
                LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->id = $row['id'];
            $this->current_month = $row['current_month'];
            $this->current_year = $row['current_year'];
            $this->last_update = $row['last_update'];
            
            return true;
        }
        
        return false;
    }
    
    // Passer au mois suivant
    public function nextMonth() {
        // D'abord, lire l'état actuel
        $this->readCurrentState();
        
        // Calculer le nouveau mois et année
        $newMonth = $this->current_month + 1;
        $newYear = $this->current_year;
        
        if ($newMonth > 12) {
            $newMonth = 1;
            $newYear++;
        }
        
        // Mettre à jour l'état du jeu
        $query = "UPDATE " . $this->table_name . " 
                SET current_month = :month, 
                    current_year = :year, 
                    last_update = NOW() 
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":month", $newMonth);
        $stmt->bindParam(":year", $newYear);
        $stmt->bindParam(":id", $this->id);
        
        if ($stmt->execute()) {
            $this->current_month = $newMonth;
            $this->current_year = $newYear;
            
            return true;
        }
        
        return false;
    }
    
    // Mettre à jour les prix des actions pour le nouveau mois
    public function updateStockPrices() {
        // Récupérer toutes les actions
        $stockQuery = "SELECT id FROM stocks";
        $stockStmt = $this->conn->prepare($stockQuery);
        $stockStmt->execute();
        
        $stockModel = new Stock($this->conn);
        
        while ($row = $stockStmt->fetch(PDO::FETCH_ASSOC)) {
            $stockModel->id = $row['id'];
            $stockModel->readOne();
            
            // Calculer le nouveau prix
            $newPrice = $stockModel->calculateNewPrice();
            
            // Mettre à jour le prix
            $stockModel->updatePrice($newPrice);
            
            // Enregistrer dans l'historique
            $stockModel->recordPriceHistory($this->current_month, $this->current_year);
        }
        
        return true;
    }
    
    // Distribuer les dividendes aux joueurs qui possèdent des actions éligibles
    public function distributeDividends() {
        // Trouver les actions qui distribuent des dividendes ce mois-ci
        $query = "SELECT id, code, dividend_amount 
                FROM stocks 
                WHERE dividend_month = :current_month 
                AND dividend_amount > 0";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":current_month", $this->current_month);
        $stmt->execute();
        
        while ($stock = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Trouver tous les joueurs qui possèdent cette action
            $portfolioQuery = "SELECT player_id, quantity 
                            FROM portfolios 
                            WHERE stock_id = :stock_id 
                            AND quantity > 0";
            
            $portfolioStmt = $this->conn->prepare($portfolioQuery);
            $portfolioStmt->bindParam(":stock_id", $stock['id']);
            $portfolioStmt->execute();
            
            while ($portfolio = $portfolioStmt->fetch(PDO::FETCH_ASSOC)) {
                // Calculer le montant total des dividendes
                $dividendTotal = $stock['dividend_amount'] * $portfolio['quantity'];
                
                // Mettre à jour le solde du joueur
                $updateQuery = "UPDATE players 
                            SET balance = balance + :dividend_amount 
                            WHERE id = :player_id";
                
                $updateStmt = $this->conn->prepare($updateQuery);
                $updateStmt->bindParam(":dividend_amount", $dividendTotal);
                $updateStmt->bindParam(":player_id", $portfolio['player_id']);
                $updateStmt->execute();
                
                // Enregistrer la transaction dans l'historique
                $logQuery = "INSERT INTO transactions 
                          SET player_id = :player_id, 
                              stock_id = :stock_id, 
                              type = 'dividend', 
                              quantity = :quantity, 
                              price = :price, 
                              total_amount = :total_amount";
                
                $logStmt = $this->conn->prepare($logQuery);
                $logStmt->bindParam(":player_id", $portfolio['player_id']);
                $logStmt->bindParam(":stock_id", $stock['id']);
                $logStmt->bindParam(":quantity", $portfolio['quantity']);
                $logStmt->bindParam(":price", $stock['dividend_amount']);
                $logStmt->bindParam(":total_amount", $dividendTotal);
                $logStmt->execute();
            }
        }
        
        return true;
    }
    
    // Vérifier les conditions de fin de jeu
    public function checkGameOver() {
        $query = "SELECT id, balance FROM players";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        while ($player = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Calculer la valeur totale du portefeuille
            $portfolioQuery = "SELECT SUM(pf.quantity * s.current_price) as portfolio_value 
                            FROM portfolios pf 
                            JOIN stocks s ON pf.stock_id = s.id 
                            WHERE pf.player_id = :player_id";
            
            $portfolioStmt = $this->conn->prepare($portfolioQuery);
            $portfolioStmt->bindParam(":player_id", $player['id']);
            $portfolioStmt->execute();
            
            $portfolio = $portfolioStmt->fetch(PDO::FETCH_ASSOC);
            $totalValue = $player['balance'] + ($portfolio['portfolio_value'] ?? 0);
            
            // Si la valeur totale est inférieure à 1000€, le joueur a perdu
            if ($totalValue < 1000) {
                // Marquer le joueur comme perdant
                $updateQuery = "UPDATE players 
                            SET game_over = 1 
                            WHERE id = :player_id";
                
                $updateStmt = $this->conn->prepare($updateQuery);
                $updateStmt->bindParam(":player_id", $player['id']);
                $updateStmt->execute();
            }
        }
        
        return true;
    }
}
?>
