<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="text-center">Connexion</h3>
            </div>
            <div class="card-body">
                <form action="index.php?page=auth&action=login" method="post">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Se connecter</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <p class="mb-0">Pas encore de compte ? <a href="index.php?page=auth&action=register">S'inscrire</a></p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-5">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3>Bienvenue sur Virtual Trader</h3>
            </div>
            <div class="card-body">
                <p>Virtual Trader est un simulateur de bourse qui vous permet d'apprendre à investir sans risquer votre argent réel.</p>
                <p>Chaque joueur commence avec 10 000 € et peut acheter ou vendre des actions parmi une liste prédéfinie.</p>
                <h4>Comment ça marche ?</h4>
                <ul>
                    <li>Le jeu fonctionne en pseudo temps réel, avec une mise à jour mensuelle des prix</li>
                    <li>Les prix des actions évoluent chaque mois selon une formule aléatoire</li>
                    <li>Certaines actions versent des dividendes, à condition de les posséder à la date de distribution</li>
                    <li>Vous perdez la partie si la valeur totale de votre portefeuille descend en dessous de 1 000 €</li>
                </ul>
                <p>Connectez-vous ou inscrivez-vous pour commencer à jouer !</p>
            </div>
        </div>
    </div>
</div>
