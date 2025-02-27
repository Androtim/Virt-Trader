<div class="row mb-4">
    <div class="col-md-8">
        <h2>Historique des transactions</h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="index.php?page=game&action=dashboard" class="btn btn-outline-primary">Retour au tableau de bord</a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Transactions</h4>
                <span class="badge bg-info"><?= count($allTransactions) ?> transactions</span>
            </div>
            <div class="card-body">
                <?php if (empty($allTransactions)): ?>
                    <p class="text-muted">Aucune transaction à afficher.</p>
                <?php else: ?>
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
                                <?php foreach ($allTransactions as $transaction): ?>
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
                                        <td>
                                            <?php 
                                            if ($transaction['type'] === 'buy') {
                                                echo '<span class="text-danger">-' . number_format($transaction['total_amount'], 2, ',', ' ') . ' €</span>';
                                            } else {
                                                echo '<span class="text-success">+' . number_format($transaction['total_amount'], 2, ',', ' ') . ' €</span>';
                                            }
                                            ?>
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
