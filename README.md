# Virtual Trader - Simulateur de Bourse

Virtual Trader est une application web de simulation boursière développée en PHP et MySQL qui permet aux utilisateurs d'apprendre à investir sans risquer leur argent réel.

## Table des matières

- [Description](#description)
- [Fonctionnalités](#fonctionnalités)
- [Captures d'écran](#captures-décran)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Configuration](#configuration)
- [Utilisation](#utilisation)
- [Structure du projet](#structure-du-projet)
- [Règles du jeu](#règles-du-jeu)
- [Développement](#développement)
- [Licence](#licence)

## Description

L'application permet aux utilisateurs de :
- Créer un compte et se connecter
- Acheter et vendre des actions virtuelles
- Suivre l'évolution de leur portefeuille
- Recevoir des dividendes sur certaines actions
- Voir le classement des joueurs
- Suivre l'historique de leurs transactions

## Fonctionnalités

- **Système d'authentification** : Inscription, connexion et déconnexion
- **Gestion du portefeuille** : Achat, vente et suivi des actions
- **Évolution dynamique des marchés** : Les prix des actions évoluent chaque mois
- **Dividendes** : Certaines actions versent des dividendes mensuels
- **Classement** : Système de classement des joueurs par valeur totale
- **Visualisations** : Graphiques d'évolution des prix et répartition du portefeuille

## Captures d'écran

*Ajoutez vos propres captures d'écran ici*

## Prérequis

- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Extension PDO PHP activée
- Un serveur web (Apache, Nginx, etc.)
- Composer (recommandé pour les dépendances)

## Installation

### 1. Cloner le dépôt

```bash
git clone https://github.com/votre-utilisateur/virtual-trader.git
cd virtual-trader
```

### 2. Créer la base de données

Connectez-vous à MySQL :

```bash
mysql -u root -p
```

Créez une nouvelle base de données :

```sql
CREATE DATABASE virtual_trader;
USE virtual_trader;
```

### 3. Importer le schéma de la base de données

```bash
mysql -u root -p virtual_trader < database-schema.sql
```

Alternativement, vous pouvez importer le fichier à l'aide de phpMyAdmin ou d'un autre outil de gestion de bases de données.

### 4. Configurer la connexion à la base de données

Modifiez le fichier `config/database.php` pour qu'il corresponde à vos paramètres de connexion :

```php
<?php
class Database {
    private $host = "localhost";      // Votre hôte MySQL
    private $db_name = "virtual_trader"; // Nom de la base de données
    private $username = "root";       // Votre nom d'utilisateur MySQL
    private $password = "";           // Votre mot de passe MySQL
    private $conn;

    // Reste du code...
}
?>
```

### 5. Configurer les paramètres de l'application

Modifiez le fichier `config/config.php` selon vos besoins :

```php
<?php
// Configuration de l'application
return [
    // Configuration de l'application
    'app' => [
        'name' => 'Virtual Trader',
        'description' => 'Simulateur de bourse virtuelle',
        'version' => '1.0.0',
        'debug' => false,        // Mettre à true pendant le développement
        'timezone' => 'Europe/Paris',
        'locale' => 'fr_FR',
    ],
    
    // Autres configurations...
];
```

### 6. Configurer le serveur web

#### Pour Apache

Assurez-vous que le fichier `.htaccess` à la racine du projet contient les règles de réécriture nécessaires. Si vous utilisez Apache, activez le module `mod_rewrite` :

```bash
sudo a2enmod rewrite
sudo service apache2 restart
```

Configurez un virtualhost dans votre configuration Apache :

```apache
<VirtualHost *:80>
    ServerName virtual-trader.local
    DocumentRoot /chemin/vers/virtual-trader
    
    <Directory /chemin/vers/virtual-trader>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/virtual-trader-error.log
    CustomLog ${APACHE_LOG_DIR}/virtual-trader-access.log combined
</VirtualHost>
```

#### Pour Nginx

```nginx
server {
    listen 80;
    server_name virtual-trader.local;
    root /chemin/vers/virtual-trader;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
    }
}
```

### 7. Configurer le fichier hosts (optionnel)

Ajoutez l'entrée suivante à votre fichier `/etc/hosts` (Linux/Mac) ou `C:\Windows\System32\drivers\etc\hosts` (Windows) :

```
127.0.0.1   virtual-trader.local
```

## Configuration

### Paramètres du jeu

Vous pouvez modifier les paramètres du jeu dans le fichier `config/config.php` :

```php
// Configuration du jeu
'game' => [
    'initial_balance' => 10000.00,     // Solde initial pour les nouveaux joueurs
    'min_balance' => 1000.00,          // Solde minimum avant Game Over
    'price_variation_min' => -10,      // Variation minimale du prix (en %)
    'price_variation_max' => 10,       // Variation maximale du prix (en %)
    'random_factor_min' => -3,         // Facteur aléatoire minimal
    'random_factor_max' => 3,          // Facteur aléatoire maximal
],
```

### Sécurité

Configurez les options de sécurité selon vos besoins :

```php
// Configuration de sécurité
'security' => [
    'session_name' => 'virtual_trader_session',
    'password_algo' => PASSWORD_BCRYPT,
    'password_options' => [
        'cost' => 12,           // Coût du hachage bcrypt (augmenter pour plus de sécurité)
    ],
    'csrf_protection' => true,  // Protection contre les attaques CSRF
],
```

## Utilisation

1. Accédez à l'application via votre navigateur web : `http://virtual-trader.local` ou `http://localhost/virtual-trader`
2. Créez un compte utilisateur
3. Connectez-vous avec vos identifiants
4. Commencez à investir dans des actions virtuelles
5. Suivez l'évolution de votre portefeuille au fil du temps

### Fonctionnalités principales

- **Tableau de bord** : Vue d'ensemble de votre portefeuille et transactions récentes
- **Actions** : Liste des actions disponibles pour achat/vente
- **Classement** : Classement des joueurs par valeur totale de portefeuille
- **Historique** : Historique complet de vos transactions

## Structure du projet

```
virtual-trader/
├── assets/               # Fichiers CSS, JS et images
├── config/               # Configuration de la base de données et de l'application
├── controllers/          # Contrôleurs de l'application
├── models/               # Modèles de données
├── includes/             # Fichiers d'en-tête et pied de page
├── views/                # Vues de l'application
├── index.php             # Point d'entrée de l'application
├── .htaccess             # Configuration Apache
└── README.md             # Documentation
```

## Règles du jeu

- Chaque joueur commence avec un capital de 10 000 €
- Les prix des actions évoluent chaque mois de manière pseudo-aléatoire (entre -10% et +10%)
- Certaines actions versent des dividendes mensuels
- Un joueur perd la partie si la valeur totale de son portefeuille descend en dessous de 1 000 €
- Le classement est établi en fonction de la valeur totale du portefeuille (solde + valeur des actions)

## Développement

Ce projet a été développé dans le cadre d'un exercice d'apprentissage du développement web avec PHP et MySQL. Il met en œuvre un modèle d'architecture MVC (Modèle-Vue-Contrôleur).

## Dépannage

### Problèmes courants

- **Erreur de connexion à la base de données** : Vérifiez que les informations de connexion dans `config/database.php` sont correctes.
- **Erreur 404** : Assurez-vous que la réécriture d'URL est correctement configurée dans votre serveur web.
- **Problèmes d'affichage** : Vérifiez que les fichiers CSS et JS sont correctement chargés.

### Journaux d'erreurs

Les journaux d'erreurs se trouvent dans le dossier `logs/` à la racine du projet.
