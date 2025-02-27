<div class="row mb-4">
    <div class="col-md-8">
        <h2>Profil de <?= $this->player->username ?></h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="index.php?page=game&action=dashboard" class="btn btn-outline-primary">Retour au tableau de bord</a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h4>Informations du joueur</h4>
            </div>
            <div class="card-body">
                <p><strong>Nom d'utilisateur :</strong> <?= $this->player->username ?></p>
                <?php if ($playerId == $_SESSION['player_id']): ?>
                    <p><strong>Email :</strong> <?= $this->player->email ?></p>
                <?php endif; ?>
                <p><strong>Date d'inscription :</strong> <?= date('d/m/Y', strtotime($this->player->registration_date)) ?></p>
                <?php if ($this->player->last_login): ?>
                    <p><strong>Dernière connexion :</strong> <?= date('d/m/Y H:i', strtotime($this->player->last_login)) ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <h4>Résumé financier</h4>
            </div>
            <div class="card-body">
                <p><strong>Solde disponible :</strong> <?= number_format($this->player->balance, 2, ',', ' ') ?> €</p>
                <p><strong>Valeur des actions :</strong> <?= number_format($totalPortfolioValue, 2, ',', ' ') ?> €</p>
                <p><strong>Valeur totale :</strong> <?= number_format($this->player->balance + $totalPortfolioValue, 2, ',', ' ') ?> €</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h4>Portefeuille d'actions</h4>
            </div>
            <div class="card-body">
                <?php if (empty($portfolioItems)): ?>
                    <p class="text-muted">Ce joueur ne possède pas encore d'actions.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Nom</th>
                                    <th>Quantité</th>
                                    <th>Prix d'achat</th>
                                    <th>Prix actuel</th>
                                    <th>Variation</th>
                                    <th>Valeur</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($portfolioItems as $item): ?>
                                    <tr>
                                        <td><?= $item['code'] ?></td>
                                        <td><?= $item['name'] ?></td>
                                        <td><?= $item['quantity'] ?></td>
                                        <td><?= number_format($item['purchase_price'], 2, ',', ' ') ?> €</td>
                                        <td><?= number_format($item['current_price'], 2, ',', ' ') ?> €</td>
                                        <td>
                                            <?php 
                                            $variation = (($item['current_price'] - $item['purchase_price']) / $item['purchase_price']) * 100;
                                            $class = $variation >= 0 ? 'text-success' : 'text-danger';
                                            $icon = $variation >= 0 ? '↑' : '↓';
                                            echo "<span class='$class'>$icon " . number_format(abs($variation), 2) . "%</span>";
                                            ?>
                                        </td>
                                        <td><?= number_format($item['total_value'], 2, ',', ' ') ?> €</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if ($playerId == $_SESSION['player_id'] && !empty($transactions)): ?>
            <div class="card">
                <div class="card-header">
                    <h4>Transactions récentes</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Action</th>
                                    <th>Type</th>
                                    <th>Quantité</th>
                                    <th>Prix</th>
                                    <th>Montant total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($transactions, 0, 10) as $transaction): ?>
                                    <tr>
                                        <td><?= date('d/m/Y H:i', strtotime($transaction['transaction_date'])) ?></td>
                                        <td><?= $transaction['code'] ?></td>
                                        <td>
                                            <?php 
                                            switch ($transaction['type']) {
                                                case 'buy':
                                                    echo '<span class="badge bg-success">Achat</span>';
                                                    break;
                                                case 'sell':
                                                    echo '<span class="badge bg-danger">Vente</span>';
                                                    break;
                                                case 'dividend':
                                                    echo '<span class="badge bg-info">Dividende</span>';
                                                    break;
                                                default:
                                                    echo $transaction['type'];
                                            }
                                            ?>
                                        </td>
                                        <td><?= $transaction['quantity'] ?></td>
                                        <td><?= number_format($transaction['price'], 2, ',', ' ') ?> €</td>
                                        <td><?= number_format($transaction['total_amount'], 2, ',', ' ') ?> €</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (count($transactions) > 10): ?>
                        <div class="text-center mt-3">
                            <a href="index.php?page=game&action=history" class="btn btn-outline-primary">Voir tout l'historique</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
