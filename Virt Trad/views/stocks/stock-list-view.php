<div class="row mb-4">
    <div class="col-md-6">
        <h2>Liste des actions</h2>
    </div>
    <div class="col-md-6">
        <form action="index.php" method="get" class="d-flex">
            <input type="hidden" name="page" value="stocks">
            <input type="hidden" name="action" value="list">
            <input type="text" name="search" class="form-control me-2" placeholder="Rechercher par code ou nom..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            <button type="submit" class="btn btn-primary">Rechercher</button>
        </form>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>Filtres</h4>
            </div>
            <div class="card-body">
                <form action="index.php" method="get" class="row g-3">
                    <input type="hidden" name="page" value="stocks">
                    <input type="hidden" name="action" value="list">
                    
                    <div class="col-md-3">
                        <label for="min_price" class="form-label">Prix min (€)</label>
                        <input type="number" class="form-control" id="min_price" name="min_price" min="0" step="1" value="<?= isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : '0' ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="max_price" class="form-label">Prix max (€)</label>
                        <input type="number" class="form-control" id="max_price" name="max_price" min="0" step="1" value="<?= isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : '10000' ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="sort" class="form-label">Trier par</label>
                        <select class="form-select" id="sort" name="sort">
                            <option value="code" <?= (!isset($_GET['sort']) || $_GET['sort'] == 'code') ? 'selected' : '' ?>>Code</option>
                            <option value="name" <?= (isset($_GET['sort']) && $_GET['sort'] == 'name') ? 'selected' : '' ?>>Nom</option>
                            <option value="price_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : '' ?>>Prix (croissant)</option>
                            <option value="price_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : '' ?>>Prix (décroissant)</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Appliquer les filtres</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Actions disponibles</h4>
                <span class="badge bg-info"><?= count($stocks) ?> actions trouvées</span>
            </div>
            <div class="card-body">
                <?php if (empty($stocks)): ?>
                    <p class="text-muted">Aucune action trouvée.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Nom</th>
                                    <th>Prix actuel</th>
                                    <th>Dividende</th>
                                    <th>Quantité possédée</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stocks as $stock): ?>
                                    <tr>
                                        <td><?= $stock['code'] ?></td>
                                        <td><?= $stock['name'] ?></td>
                                        <td><?= number_format($stock['current_price'], 2, ',', ' ') ?> €</td>
                                        <td>
                                            <?php if ($stock['dividend_amount'] > 0): ?>
                                                <?= number_format($stock['dividend_amount'], 2, ',', ' ') ?> € 
                                                (Mois: <?= date("F", mktime(0, 0, 0, $stock['dividend_month'], 1)) ?>)
                                            <?php else: ?>
                                                <span class="text-muted">Aucun</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            if (isset($playerStocks[$stock['id']])) {
                                                echo $playerStocks[$stock['id']]['quantity'];
                                            } else {
                                                echo "<span class='text-muted'>0</span>";
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="index.php?page=stocks&action=details&id=<?= $stock['id'] ?>" class="btn btn-sm btn-outline-primary">Détails</a>
                                            
                                            <?php if (isset($playerStocks[$stock['id']])): ?>
                                                <a href="index.php?page=stocks&action=details&id=<?= $stock['id'] ?>#sell" class="btn btn-sm btn-outline-danger">Vendre</a>
                                            <?php endif; ?>
                                            
                                            <a href="index.php?page=stocks&action=details&id=<?= $stock['id'] ?>#buy" class="btn btn-sm btn-outline-success">Acheter</a>
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
