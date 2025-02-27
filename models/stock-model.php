<?php
// models/Stock.php
class Stock {
    private $conn;
    private $table_name = "stocks";
    
    public $id;
    public $code;
    public $name;
    public $description;
    public $initial_price;
    public $current_price;
    public $dividend_amount;
    public $dividend_month;
    public $last_update;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Lire toutes les actions
    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY code";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Lire une action spécifique
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->id = $row['id'];
            $this->code = $row['code'];
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->initial_price = $row['initial_price'];
            $this->current_price = $row['current_price'];
            $this->dividend_amount = $row['dividend_amount'];
            $this->dividend_month = $row['dividend_month'];
            $this->last_update = $row['last_update'];
            
            return true;
        }
        
        return false;
    }
    
    // Mettre à jour le prix d'une action
    public function updatePrice($newPrice) {
        $query = "UPDATE " . $this->table_name . " 
                SET current_price = :price, 
                    last_update = NOW() 
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":price", $newPrice);
        $stmt->bindParam(":id", $this->id);
        
        if ($stmt->execute()) {
            $this->current_price = $newPrice;
            return true;
        }
        
        return false;
    }
    
    // Enregistrer l'historique des prix
    public function recordPriceHistory($gameMonth, $gameYear) {
        $query = "INSERT INTO price_history 
                SET stock_id = :stock_id, 
                    price = :price, 
                    game_month = :game_month, 
                    game_year = :game_year";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":stock_id", $this->id);
        $stmt->bindParam(":price", $this->current_price);
        $stmt->bindParam(":game_month", $gameMonth);
        $stmt->bindParam(":game_year", $gameYear);
        
        return $stmt->execute();
    }
    
    // Obtenir l'historique des prix d'une action
    public function getPriceHistory($months = 12) {
        $query = "SELECT price, game_month, game_year, real_date 
                FROM price_history 
                WHERE stock_id = :stock_id 
                ORDER BY game_year DESC, game_month DESC 
                LIMIT :months";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":stock_id", $this->id);
        $stmt->bindParam(":months", $months, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Rechercher des actions par nom ou code
    public function search($keywords) {
        $query = "SELECT * FROM " . $this->table_name . " 
                WHERE code LIKE :keywords 
                OR name LIKE :keywords 
                ORDER BY code";
        
        $stmt = $this->conn->prepare($query);
        
        $keywords = "%{$keywords}%";
        $stmt->bindParam(":keywords", $keywords);
        
        $stmt->execute();
        
        return $stmt;
    }
    
    // Filtrer les actions par plage de prix
    public function filterByPrice($minPrice, $maxPrice) {
        $query = "SELECT * FROM " . $this->table_name . " 
                WHERE current_price >= :min_price 
                AND current_price <= :max_price 
                ORDER BY current_price";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":min_price", $minPrice);
        $stmt->bindParam(":max_price", $maxPrice);
        
        $stmt->execute();
        
        return $stmt;
    }
    
    // Calculer le nouveau prix pour le prochain tour
    public function calculateNewPrice() {
        // Générer une variation aléatoire entre -3% et +3%
        $variation = mt_rand(-30, 30) / 10;
        
        // Ajouter une valeur aléatoire supplémentaire entre -0.5% et +0.5%
        $extraVariation = mt_rand(-5, 5) / 10;
        
        $totalVariation = $variation + $extraVariation;
        
        // Limiter la variation totale entre -10% et +10%
        $totalVariation = max(-10, min(10, $totalVariation));
        
        // Calculer le nouveau prix
        $newPrice = $this->current_price * (1 + ($totalVariation / 100));
        
        // S'assurer que le prix ne descend pas en dessous de 1€
        $newPrice = max(1, $newPrice);
        
        return round($newPrice, 2);
    }
}
?>
