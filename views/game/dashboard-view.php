<div class="row mb-4">
    <div class="col-md-6">
        <h2>Tableau de bord</h2>
    </div>
    <div class="col-md-6 text-end">
        <h4>Date actuelle : <?= date("F Y", mktime(0, 0, 0, $this->game->current_month, 1, $this->game->current_year)) ?></h4>
        <form action="index.php?page=game&action=next_turn" method="post" class="mt-2">
            <button type="submit" class="btn btn-primary">Passer au mois suivant</button>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h4>Résumé du portefeuille</h4>
            </div>
            <div class="card-body">
                <p><strong>Solde disponible :</strong> <?= number_format($this->player->balance, 2, ',', ' ') ?> €</p>
                <p><strong>Valeur des actions :</strong> <?= number_format($totalPortfolioValue, 2, ',', ' ') ?> €</p>
                <p><strong>Valeur totale :</strong> <?= number_format($this->player->balance + $totalPortfolioValue, 2, ',', ' ') ?> €</p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h4>Transactions récentes</h4>
            </div>
            <div class="card-body">
                <?php if (empty($recentTransactions)): ?>
                    <p class="text-muted">Aucune transaction récente.</p>
                <?php else: ?>
                    <ul class="list-group">
                        <?php foreach ($recentTransactions as $transaction): ?>
                            <li class="list-group-item">
                                <strong><?= ucfirst($transaction['type']) ?></strong> -
                                <?= $transaction['quantity'] ?> x <?= $transaction['code'] ?>
                                (<?= number_format($transaction['price'], 2, ',', ' ') ?> €)
                                <div class="text-muted small">
                                    <?= date('d/m/Y H:i', strtotime($transaction['transaction_date'])) ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="mt-3">
                        <a href="index.php?page=game&action=history" class="btn btn-sm btn-outline-primary">Voir tout l'historique</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h4>Mes actions</h4>
            </div>
            <div class="card-body">
                <?php if (empty($portfolioItems)): ?>
                    <p class="text-muted">Vous ne possédez pas encore d'actions.</p>
                    <a href="index.php?page=stocks&action=list" class="btn btn-primary">Acheter des actions</a>
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
                                    <th>Actions</th>
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
                                        <td>
                                            <a href="index.php?page=stocks&action=details&id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-primary">Détails</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h4>Répartition du portefeuille</h4>
            </div>
            <div class="card-body">
                <?php if (empty($portfolioItems)): ?>
                    <p class="text-muted">Aucune donnée à afficher.</p>
                <?php else: ?>
                    <canvas id="portfolioChart" width="400" height="200"></canvas>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const ctx = document.getElementById('portfolioChart').getContext('2d');
                            const portfolioChart = new Chart(ctx, {
                                type: 'pie',
                                data: {
                                    labels: [
                                        <?php foreach ($portfolioItems as $item): ?>
                                            '<?= $item['code'] ?>',
                                        <?php endforeach; ?>
                                    ],
                                    datasets: [{
                                        data: [
                                            <?php foreach ($portfolioItems as $item): ?>
                                                <?= $item['total_value'] ?>,
                                            <?php endforeach; ?>
                                        ],
                                        backgroundColor: [
                                            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                                            '#858796', '#6f42c1', '#5a5c69', '#fd7e14', '#20c997'
                                        ],
                                        hoverBackgroundColor: [
                                            '#2e59d9', '#17a673', '#2c9faf', '#f4b619', '#e02d1b',
                                            '#757575', '#5d36a4', '#4e4f5c', '#ec6c0e', '#199d76'
                                        ],
                                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                                    }],
                                },
                                options: {
                                    maintainAspectRatio: false,
                                    tooltips: {
                                        backgroundColor: "rgb(255,255,255)",
                                        bodyFontColor: "#858796",
                                        borderColor: '#dddfeb',
                                        borderWidth: 1,
                                        xPadding: 15,
                                        yPadding: 15,
                                        displayColors: false,
                                        caretPadding: 10,
                                        callbacks: {
                                            label: function(tooltipItem, data) {
                                                var indice = tooltipItem.index;
                                                return data.labels[indice] + ': ' + parseFloat(data.datasets[0].data[indice]).toLocaleString('fr-FR', {
                                                    style: 'currency',
                                                    currency: 'EUR',
                                                    minimumFractionDigits: 2
                                                });
                                            }
                                        }
                                    },
                                    legend: {
                                        display: true,
                                        position: 'right',
                                    },
                                    cutoutPercentage: 0,
                                },
                            });
                        });
                    </script>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
