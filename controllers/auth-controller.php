<?php
// controllers/AuthController.php
class AuthController {
    private $db;
    private $player;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->player = new Player($this->db);
    }
    
    public function register($email, $username, $password) {
        // Validation des données
        if (empty($email) || empty($username) || empty($password)) {
            $_SESSION['error'] = "Tous les champs sont obligatoires.";
            header("Location: index.php?page=auth&action=register");
            exit;
        }
        
        // Vérifier la validité de l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "L'adresse email n'est pas valide.";
            header("Location: index.php?page=auth&action=register");
            exit;
        }
        
        // Vérifier la longueur du mot de passe
        if (strlen($password) < 6) {
            $_SESSION['error'] = "Le mot de passe doit contenir au moins 6 caractères.";
            header("Location: index.php?page=auth&action=register");
            exit;
        }
        
        // Définir les propriétés du joueur
        $this->player->email = $email;
        $this->player->username = $username;
        $this->player->password = $password;
        
        // Créer le joueur
        if ($this->player->create()) {
            $_SESSION['success'] = "Compte créé avec succès ! Connectez-vous maintenant.";
            header("Location: index.php?page=auth&action=login");
            exit;
        } else {
            $_SESSION['error'] = "Cette adresse email est déjà utilisée.";
            header("Location: index.php?page=auth&action=register");
            exit;
        }
    }
    
    public function login($email, $password) {
        // Validation des données
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = "Veuillez saisir votre email et votre mot de passe.";
            header("Location: index.php?page=auth&action=login");
            exit;
        }
        
        // Définir les propriétés du joueur
        $this->player->email = $email;
        $this->player->password = $password;
        
        // Tentative de connexion
        if ($this->player->login()) {
            // Stocker les informations du joueur en session
            $_SESSION['player_id'] = $this->player->id;
            $_SESSION['username'] = $this->player->username;
            $_SESSION['balance'] = $this->player->balance;
            
            header("Location: index.php?page=game&action=dashboard");
            exit;
        } else {
            $_SESSION['error'] = "Email ou mot de passe incorrect.";
            header("Location: index.php?page=auth&action=login");
            exit;
        }
    }
    
    public function logout() {
        // Détruire toutes les variables de session
        session_unset();
        session_destroy();
        
        // Rediriger vers la page de connexion
        header("Location: index.php");
        exit;
    }
}
?>
