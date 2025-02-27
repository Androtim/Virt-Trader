<div class="row mb-4">
    <div class="col-md-8">
        <h2><?= $this->stock->code ?> - <?= $this->stock->name ?></h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="index.php?page=stocks&action=list" class="btn btn-outline-primary">Retour à la liste</a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h4>Détails de l'action</h4>
            </div>
            <div class="card-body">
                <p><?= $this->stock->description ?></p>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong>Prix actuel :</strong> <?= number_format($this->stock->current_price, 2, ',', ' ') ?> €</p>
                        <p><strong>Prix initial :</strong> <?= number_format($this->stock->initial_price, 2, ',', ' ') ?> €</p>
                        <p>
                            <strong>Variation depuis le début :</strong>
                            <?php 
                            $variation = (($this->stock->current_price - $this->stock->initial_price) / $this->stock->initial_price) * 100;
                            $class = $variation >= 0 ? 'text-success' : 'text-danger';
                            $icon = $variation >= 0 ? '↑' : '↓';
                            echo "<span class='$class'>$icon " . number_format(abs($variation), 2) . "%</span>";
                            ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <?php if ($this->stock->dividend_amount > 0): ?>
                            <p><strong>Dividende :</strong> <?= number_format($this->stock->dividend_amount, 2, ',', ' ') ?> € par action</p>
                            <p><strong>Mois de distribution :</strong> <?= date("F", mktime(0, 0, 0, $this->stock->dividend_month, 1)) ?></p>
                            <p><strong>Rendement du dividende :</strong> <?= number_format(($this->stock->dividend_amount / $this->stock->current_price) * 100, 2) ?>%</p>
                        <?php else: ?>
                            <p><strong>Dividende :</strong> Cette action ne verse pas de dividende</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <h5>Évolution du prix sur les 12 derniers mois</h5>
                <?php if (empty($historyData)): ?>
                    <p class="text-muted">Pas d'historique disponible.</p>
                <?php else: ?>
                    <canvas id="priceHistoryChart" width="400" height="200"></canvas>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const ctx = document.getElementById('priceHistoryChart').getContext('2d');
                            const priceHistoryChart = new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: [
                                        <?php 
                                        $reversedHistory = array_reverse($historyData);
                                        foreach ($reversedHistory as $item): 
                                            echo "'" . date("M Y", mktime(0, 0, 0, $item['game_month'], 1, $item['game_year'])) . "',";
                                        endforeach; 
                                        ?>
                                    ],
                                    datasets: [{
                                        label: 'Prix (€)',
                                        data: [
                                            <?php 
                                            foreach ($reversedHistory as $item): 
                                                echo $item['price'] . ',';
                                            endforeach; 
                                            ?>
                                        ],
                                        borderColor: 'rgba(78, 115, 223, 1)',
                                        backgroundColor: 'rgba(78, 115, 223, 0.1)',
                                        pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                                        pointBorderColor: '#fff',
                                        pointHoverBackgroundColor: '#fff',
                                        pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                                        borderWidth: 2,
                                        fill: true
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    scales: {
                                        y: {
                                            beginAtZero: false,
                                            ticks: {
                                                callback: function(value) {
                                                    return value.toLocaleString('fr-FR', {
                                                        style: 'currency',
                                                        currency: 'EUR',
                                                        minimumFractionDigits: 2
                                                    });
                                                }
                                            }
                                        }
                                    },
                                    plugins: {
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    return context.dataset.label + ': ' + context.parsed.y.toLocaleString('fr-FR', {
                                                        style: 'currency',
                                                        currency: 'EUR',
                                                        minimumFractionDigits: 2
                                                    });
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        });
                    </script>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h4>Votre position</h4>
            </div>
            <div class="card-body">
                <?php if ($ownedQuantity > 0): ?>
                    <p><strong>Quantité possédée :</strong> <?= $ownedQuantity ?></p>
                    <p><strong>Prix d'achat moyen :</strong> <?= number_format($purchasePrice, 2, ',', ' ') ?> €</p>
                    <p>
                        <strong>Performance :</strong>
                        <?php 
                        $perf = (($this->stock->current_price - $purchasePrice) / $purchasePrice) * 100;
                        $class = $perf >= 0 ? 'text-success' : 'text-danger';
                        $icon = $perf >= 0 ? '↑' : '↓';
                        echo "<span class='$class'>$icon " . number_format(abs($perf), 2) . "%</span>";
                        ?>
                    </p>
                    <p><strong>Valeur actuelle :</strong> <?= number_format($ownedQuantity * $this->stock->current_price, 2, ',', ' ') ?> €</p>
                    <p><strong>Gain/Perte :</strong> 
                        <?php 
                        $profitLoss = $ownedQuantity * ($this->stock->current_price - $purchasePrice);
                        $class = $profitLoss >= 0 ? 'text-success' : 'text-danger';
                        echo "<span class='$class'>" . number_format($profitLoss, 2, ',', ' ') . " €</span>";
                        ?>
                    </p>
                <?php else: ?>
                    <p class="text-muted">Vous ne possédez pas cette action.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card mb-4" id="buy">
            <div class="card-header">
                <h4>Acheter</h4>
            </div>
            <div class="card-body">
                <form action="index.php?page=stocks&action=buy" method="post">
                    <input type="hidden" name="stock_id" value="<?= $this->stock->id ?>">
                    
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantité</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1" value="1" required>
                    </div>
                    
                    <div class="mb-3">
                        <p><strong>Prix unitaire :</strong> <?= number_format($this->stock->current_price, 2, ',', ' ') ?> €</p>
                        <p><strong>Solde disponible :</strong> <?= number_format($this->player->balance, 2, ',', ' ') ?> €</p>
                        <p><strong>Coût total :</strong> <span id="totalCost"><?= number_format($this->stock->current_price, 2, ',', ' ') ?></span> €</p>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success" <?= ($this->player->balance < $this->stock->current_price) ? 'disabled' : '' ?>>Acheter</button>
                    </div>
                </form>
                
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const quantityInput = document.getElementById('quantity');
                        const totalCostSpan = document.getElementById('totalCost');
                        const price = <?= $this->stock->current_price ?>;
                        
                        quantityInput.addEventListener('input', function() {
                            const quantity = parseInt(this.value) || 0;
                            const totalCost = quantity * price;
                            totalCostSpan.textContent = totalCost.toLocaleString('fr-FR', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        });
                    });
                </script>
            </div>
        </div>
        
        <?php if ($ownedQuantity > 0): ?>
            <div class="card" id="sell">
                <div class="card-header">
                    <h4>Vendre</h4>
                </div>
                <div class="card-body">
                    <form action="index.php?page=stocks&action=sell" method="post">
                        <input type="hidden" name="stock_id" value="<?= $this->stock->id ?>">
                        
                        <div class="mb-3">
                            <label for="sell_quantity" class="form-label">Quantité</label>
                            <input type="number" class="form-control" id="sell_quantity" name="quantity" min="1" max="<?= $ownedQuantity ?>" value="1" required>
                        </div>
                        
                        <div class="mb-3">
                            <p><strong>Prix unitaire :</strong> <?= number_format($this->stock->current_price, 2, ',', ' ') ?> €</p>
                            <p><strong>Quantité possédée :</strong> <?= $ownedQuantity ?></p>
                            <p><strong>Montant total :</strong> <span id="totalAmount"><?= number_format($this->stock->current_price, 2, ',', ' ') ?></span> €</p>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-danger">Vendre</button>
                        </div>
                    </form>
                    
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const sellQuantityInput = document.getElementById('sell_quantity');
                            const totalAmountSpan = document.getElementById('totalAmount');
                            const price = <?= $this->stock->current_price ?>;
                            
                            sellQuantityInput.addEventListener('input', function() {
                                const quantity = parseInt(this.value) || 0;
                                const totalAmount = quantity * price;
                                totalAmountSpan.textContent = totalAmount.toLocaleString('fr-FR', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            });
                        });
                    </script>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
