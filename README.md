# Virtual Trader

Virtual Trader est une application web de simulation boursière développée en PHP et MySQL qui permet aux utilisateurs d'apprendre à investir sans risquer leur argent réel.

## Description

L'application permet aux utilisateurs de:
- Créer un compte et se connecter
- Acheter et vendre des actions virtuelles
- Suivre l'évolution de leur portefeuille
- Recevoir des dividendes sur certaines actions
- Voir le classement des joueurs
- Suivre l'historique de leurs transactions

## Fonctionnalités

- **Système d'authentification**: Inscription, connexion et déconnexion
- **Gestion du portefeuille**: Achat, vente et suivi des actions
- **Évolution dynamique des marchés**: Les prix des actions évoluent chaque mois
- **Dividendes**: Certaines actions versent des dividendes mensuels
- **Classement**: Système de classement des joueurs par valeur totale
- **Visualisations**: Graphiques d'évolution des prix et répartition du portefeuille

## Installation

1. Cloner ce dépôt dans votre serveur web:
   ```
   git clone https://github.com/Androtim/Virt-Trader.git
   ```

2. Créer une base de données MySQL et importer le fichier `database.sql`

3. Configurer les paramètres de connexion à la base de données dans `config/database.php`

4. Accéder à l'application via votre navigateur web

## Configuration requise

- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Extension PDO PHP activée
- Un serveur web (Apache, Nginx, etc.)

## Structure du projet

```
virtual-trader/
├── assets/               # Fichiers CSS, JS et images
├── config/               # Configuration de la base de données
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

## License

Ce projet est sous licence MIT. Voir le fichier LICENSE pour plus de détails.
