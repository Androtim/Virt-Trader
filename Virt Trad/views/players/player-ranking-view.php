<div class="row mb-4">
    <div class="col-md-12">
        <h2>Classement des joueurs</h2>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Classement par valeur totale</h4>
                <span class="badge bg-info"><?= count($ranking) ?> joueurs</span>
            </div>
            <div class="card-body">
                <?php if (empty($ranking)): ?>
                    <p class="text-muted">Aucun joueur à afficher.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Rang</th>
                                    <th>Joueur</th>
                                    <th>Solde</th>
                                    <th>Valeur du portefeuille</th>
                                    <th>Valeur totale</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ranking as $player): ?>
                                    <tr <?= ($player['id'] == $_SESSION['player_id']) ? 'class="table-primary"' : '' ?>>
                                        <td><?= $player['rank'] ?></td>
                                        <td><?= $player['username'] ?></td>
                                        <td><?= number_format($player['balance'], 2, ',', ' ') ?> €</td>
                                        <td><?= number_format($player['portfolio_value'], 2, ',', ' ') ?> €</td>
                                        <td><strong><?= number_format($player['total_value'], 2, ',', ' ') ?> €</strong></td>
                                        <td>
                                            <a href="index.php?page=players&action=profile&id=<?= $player['id'] ?>" class="btn btn-sm btn-outline-primary">Voir profil</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
