<?php
// controllers/StockController.php
class StockController {
    private $db;
    private $stock;
    private $player;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->stock = new Stock($this->db);
        $this->player = new Player($this->db);
    }
    
    public function listStocks() {
        // Récupérer les paramètres de filtrage
        $search = isset($_GET['search']) ? $_GET['search'] : "";
        $minPrice = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
        $maxPrice = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 10000;
        $sortBy = isset($_GET['sort']) ? $_GET['sort'] : "code";
        
        // Récupérer les stocks selon les filtres
        if (!empty($search)) {
            $result = $this->stock->search($search);
        } elseif ($minPrice > 0 || $maxPrice < 10000) {
            $result = $this->stock->filterByPrice($minPrice, $maxPrice);
        } else {
            $result = $this->stock->read();
        }
        
        $stocks = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $stocks[] = $row;
        }
        
        // Récupérer le portefeuille du joueur actuel
        $this->player->id = $_SESSION['player_id'];
        $portfolio = $this->player->getPortfolio();
        
        $playerStocks = [];
        while ($row = $portfolio->fetch(PDO::FETCH_ASSOC)) {
            $playerStocks[$row['id']] = $row;
        }
        
        // Inclure la vue de liste des actions
        include_once "views/stocks/list.php";
    }
    
    public function details($stockId) {
        if (!$stockId) {
            $_SESSION['error'] = "Action non spécifiée.";
            header("Location: index.php?page=stocks&action=list");
            exit;
        }
        
        // Récupérer les détails de l'action
        $this->stock->id = $stockId;
        if ($this->stock->readOne()) {
            // Récupérer l'historique des prix
            $priceHistory = $this->stock->getPriceHistory(12);
            $historyData = [];
            
            while ($row = $priceHistory->fetch(PDO::FETCH_ASSOC)) {
                $historyData[] = $row;
            }
            
            // Récupérer la quantité possédée par le joueur
            $this->player->id = $_SESSION['player_id'];
            $portfolio = $this->player->getPortfolio();
            
            $ownedQuantity = 0;
            $purchasePrice = 0;
            
            while ($row = $portfolio->fetch(PDO::FETCH_ASSOC)) {
                if ($row['id'] == $stockId) {
                    $ownedQuantity = $row['quantity'];
                    $purchasePrice = $row['purchase_price'];
                    break;
                }
            }
            
            // Inclure la vue des détails d'une action
            include_once "views/stocks/details.php";
        } else {
            $_SESSION['error'] = "Action introuvable.";
            header("Location: index.php?page=stocks&action=list");
            exit;
        }
    }
    
    public function buy($stockId, $quantity) {
        // Validation des données
        if (!$stockId || !is_numeric($quantity) || $quantity <= 0) {
            $_SESSION['error'] = "Quantité invalide.";
            header("Location: index.php?page=stocks&action=details&id=" . $stockId);
            exit;
        }
        
        // Récupérer les détails de l'action
        $this->stock->id = $stockId;
        if (!$this->stock->readOne()) {
            $_SESSION['error'] = "Action introuvable.";
            header("Location: index.php?page=stocks&action=list");
            exit;
        }
        
        // Récupérer le joueur actuel
        $this->player->id = $_SESSION['player_id'];
        $this->player->readOne();
        
        // Calculer le coût total
        $totalCost = $this->stock->current_price * $quantity;
        
        // Vérifier si le joueur a assez d'argent
        if ($this->player->balance < $totalCost) {
            $_SESSION['error'] = "Solde insuffisant pour effectuer cet achat.";
            header("Location: index.php?page=stocks&action=details&id=" . $stockId);
            exit;
        }
        
        // Déduire le montant du solde du joueur
        $this->player->updateBalance(-$totalCost);
        
        // Vérifier si le joueur possède déjà cette action
        $query = "SELECT id, quantity, purchase_price FROM portfolios 
                WHERE player_id = :player_id AND stock_id = :stock_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":player_id", $this->player->id);
        $stmt->bindParam(":stock_id", $this->stock->id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            // Mettre à jour la quantité existante
            $portfolio = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Calculer le nouveau prix d'achat moyen pondéré
            $totalOldValue = $portfolio['quantity'] * $portfolio['purchase_price'];
            $totalNewValue = $quantity * $this->stock->current_price;
            $totalQuantity = $portfolio['quantity'] + $quantity;
            $newAveragePrice = ($totalOldValue + $totalNewValue) / $totalQuantity;
            
            $updateQuery = "UPDATE portfolios 
                          SET quantity = quantity + :quantity, 
                              purchase_price = :purchase_price 
                          WHERE id = :id";
            
            $updateStmt = $this->db->prepare($updateQuery);
            $updateStmt->bindParam(":quantity", $quantity);
            $updateStmt->bindParam(":purchase_price", $newAveragePrice);
            $updateStmt->bindParam(":id", $portfolio['id']);
            $updateStmt->execute();
        } else {
            // Créer une nouvelle entrée dans le portefeuille
            $insertQuery = "INSERT INTO portfolios 
                          SET player_id = :player_id, 
                              stock_id = :stock_id, 
                              quantity = :quantity, 
                              purchase_price = :purchase_price";
            
            $insertStmt = $this->db->prepare($insertQuery);
            $insertStmt->bindParam(":player_id", $this->player->id);
            $insertStmt->bindParam(":stock_id", $this->stock->id);
            $insertStmt->bindParam(":quantity", $quantity);
            $insertStmt->bindParam(":purchase_price", $this->stock->current_price);
            $insertStmt->execute();
        }
        
        // Enregistrer la transaction
        $transactionQuery = "INSERT INTO transactions 
                           SET player_id = :player_id, 
                               stock_id = :stock_id, 
                               type = 'buy', 
                               quantity = :quantity, 
                               price = :price, 
                               total_amount = :total_amount";
        
        $transactionStmt = $this->db->prepare($transactionQuery);
        $transactionStmt->bindParam(":player_id", $this->player->id);
        $transactionStmt->bindParam(":stock_id", $this->stock->id);
        $transactionStmt->bindParam(":quantity", $quantity);
        $transactionStmt->bindParam(":price", $this->stock->current_price);
        $transactionStmt->bindParam(":total_amount", $totalCost);
        $transactionStmt->execute();
        
        // Mettre à jour la session
        $_SESSION['balance'] = $this->player->balance;
        $_SESSION['success'] = "Achat réussi : " . $quantity . " actions " . $this->stock->code . " pour " . number_format($totalCost, 2, ',', ' ') . " €";
        
        header("Location: index.php?page=stocks&action=details&id=" . $stockId);
        exit;
    }
    
    public function sell($stockId, $quantity) {
        // Validation des données
        if (!$stockId || !is_numeric($quantity) || $quantity <= 0) {
            $_SESSION['error'] = "Quantité invalide.";
            header("Location: index.php?page=stocks&action=details&id=" . $stockId);
            exit;
        }
        
        // Récupérer les détails de l'action
        $this->stock->id = $stockId;
        if (!$this->stock->readOne()) {
            $_SESSION['error'] = "Action introuvable.";
            header("Location: index.php?page=stocks&action=list");
            exit;
        }
        
        // Récupérer le joueur actuel
        $this->player->id = $_SESSION['player_id'];
        $this->player->readOne();
        
        // Vérifier si le joueur possède suffisamment d'actions
        $query = "SELECT id, quantity FROM portfolios 
                WHERE player_id = :player_id AND stock_id = :stock_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":player_id", $this->player->id);
        $stmt->bindParam(":stock_id", $this->stock->id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $portfolio = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($portfolio['quantity'] < $quantity) {
                $_SESSION['error'] = "Vous ne possédez pas suffisamment d'actions à vendre.";
                header("Location: index.php?page=stocks&action=details&id=" . $stockId);
                exit;
            }
            
            // Calculer le montant à créditer
            $totalAmount = $this->stock->current_price * $quantity;
            
            // Ajouter le montant au solde du joueur
            $this->player->updateBalance($totalAmount);
            
            // Mettre à jour la quantité dans le portefeuille
            $newQuantity = $portfolio['quantity'] - $quantity;
            
            if ($newQuantity > 0) {
                $updateQuery = "UPDATE portfolios 
                              SET quantity = :quantity 
                              WHERE id = :id";
                
                $updateStmt = $this->db->prepare($updateQuery);
                $updateStmt->bindParam(":quantity", $newQuantity);
                $updateStmt->bindParam(":id", $portfolio['id']);
                $updateStmt->execute();
            } else {
                // Supprimer l'entrée si la quantité est nulle
                $deleteQuery = "DELETE FROM portfolios WHERE id = :id";
                $deleteStmt = $this->db->prepare($deleteQuery);
                $deleteStmt->bindParam(":id", $portfolio['id']);
                $deleteStmt->execute();
            }
            
            // Enregistrer la transaction
            $transactionQuery = "INSERT INTO transactions 
                               SET player_id = :player_id, 
                                   stock_id = :stock_id, 
                                   type = 'sell', 
                                   quantity = :quantity, 
                                   price = :price, 
                                   total_amount = :total_amount";
            
            $transactionStmt = $this->db->prepare($transactionQuery);
            $transactionStmt->bindParam(":player_id", $this->player->id);
            $transactionStmt->bindParam(":stock_id", $this->stock->id);
            $transactionStmt->bindParam(":quantity", $quantity);
            $transactionStmt->bindParam(":price", $this->stock->current_price);
            $transactionStmt->bindParam(":total_amount", $totalAmount);
            $transactionStmt->execute();
            
            // Mettre à jour la session
            $_SESSION['balance'] = $this->player->balance;
            $_SESSION['success'] = "Vente réussie : " . $quantity . " actions " . $this->stock->code . " pour " . number_format($totalAmount, 2, ',', ' ') . " €";
        } else {
            $_SESSION['error'] = "Vous ne possédez pas cette action.";
        }
        
        header("Location: index.php?page=stocks&action=details&id=" . $stockId);
        exit;
    }
}
?>
