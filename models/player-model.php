<?php
// models/Player.php
class Player {
    private $conn;
    private $table_name = "players";
    
    public $id;
    public $email;
    public $password;
    public $username;
    public $balance;
    public $registration_date;
    public $last_login;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Créer un nouveau joueur
    public function create() {
        // Vérifier si l'email existe déjà
        if ($this->emailExists()) {
            return false;
        }
        
        $query = "INSERT INTO " . $this->table_name . " 
                SET email = :email, 
                    password = :password, 
                    username = :username";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize et bind
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
        
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":username", $this->username);
        
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    // Vérifier les identifiants
    public function login() {
        $query = "SELECT id, username, password, balance FROM " . $this->table_name . " 
                WHERE email = :email LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        
        $this->email = htmlspecialchars(strip_tags($this->email));
        
        $stmt->bindParam(":email", $this->email);
        
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($this->password, $row['password'])) {
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->balance = $row['balance'];
                
                // Mettre à jour la date de dernière connexion
                $updateQuery = "UPDATE " . $this->table_name . " 
                            SET last_login = NOW() 
                            WHERE id = :id";
                $updateStmt = $this->conn->prepare($updateQuery);
                $updateStmt->bindParam(":id", $this->id);
                $updateStmt->execute();
                
                return true;
            }
        }
        
        return false;
    }
    
    // Vérifier si l'email existe déjà
    public function emailExists() {
        $query = "SELECT id FROM " . $this->table_name . " 
                WHERE email = :email LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        
        $this->email = htmlspecialchars(strip_tags($this->email));
        
        $stmt->bindParam(":email", $this->email);
        
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    // Récupérer un joueur par ID
    public function readOne() {
        $query = "SELECT id, email, username, balance, registration_date, last_login 
                FROM " . $this->table_name . " 
                WHERE id = :id LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":id", $this->id);
        
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->id = $row['id'];
            $this->email = $row['email'];
            $this->username = $row['username'];
            $this->balance = $row['balance'];
            $this->registration_date = $row['registration_date'];
            $this->last_login = $row['last_login'];
            
            return true;
        }
        
        return false;
    }
    
    // Mettre à jour le solde du joueur
    public function updateBalance($amount) {
        $query = "UPDATE " . $this->table_name . " 
                SET balance = balance + :amount 
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":amount", $amount);
        $stmt->bindParam(":id", $this->id);
        
        if ($stmt->execute()) {
            $this->balance += $amount;
            return true;
        }
        
        return false;
    }
    
    // Obtenir le classement des joueurs
    public function getRanking() {
        $query = "SELECT p.id, p.username, p.balance, 
                  SUM(IFNULL(pf.quantity * s.current_price, 0)) as portfolio_value,
                  (p.balance + SUM(IFNULL(pf.quantity * s.current_price, 0))) as total_value
                FROM " . $this->table_name . " p
                LEFT JOIN portfolios pf ON p.id = pf.player_id
                LEFT JOIN stocks s ON pf.stock_id = s.id
                GROUP BY p.id
                ORDER BY total_value DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Obtenir le portefeuille d'un joueur
    public function getPortfolio() {
        $query = "SELECT s.id, s.code, s.name, s.current_price, 
                  pf.quantity, pf.purchase_price,
                  (s.current_price - pf.purchase_price) * pf.quantity as profit_loss,
                  (s.current_price * pf.quantity) as total_value
                FROM portfolios pf
                JOIN stocks s ON pf.stock_id = s.id
                WHERE pf.player_id = :player_id
                ORDER BY s.code";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":player_id", $this->id);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Obtenir l'historique des transactions d'un joueur
    public function getTransactionHistory() {
        $query = "SELECT t.id, t.type, t.quantity, t.price, t.total_amount, t.transaction_date,
                  s.code, s.name
                FROM transactions t
                JOIN stocks s ON t.stock_id = s.id
                WHERE t.player_id = :player_id
                ORDER BY t.transaction_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":player_id", $this->id);
        $stmt->execute();
        
        return $stmt;
    }
}
?>
