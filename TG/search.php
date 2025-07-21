<?php include 'includes/header.php'; ?>
<?php require_once 'config.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fa-solid fa-magnifying-glass"></i> Find Compatible Glass</h1>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="get" action="search.php">
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label">Brand</label>
                    <select class="form-select" name="brand">
                        <option value="">All Brands</option>
                        <?php
                        $stmt = $pdo->query("SELECT DISTINCT brand FROM products ORDER BY brand");
                        while ($row = $stmt->fetch()):
                        ?>
                        <option value="<?= $row['brand'] ?>"><?= $row['brand'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Device Model</label>
                    <input type="text" class="form-control" name="model" placeholder="e.g. iPhone 12, S21 Ultra">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa-solid fa-search"></i> Search
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if (isset($_GET['brand']) || isset($_GET['model'])): ?>
<div class="card">
    <div class="card-header">
        <h5>Search Results</h5>
    </div>
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>Type</th>
                    <th>Price (LKR)</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $brand = $_GET['brand'] ?? '';
                $model = $_GET['model'] ?? '';
                
                $sql = "SELECT * FROM products WHERE 1=1";
                $params = [];
                
                if (!empty($brand)) {
                    $sql .= " AND brand LIKE ?";
                    $params[] = "%$brand%";
                }
                
                if (!empty($model)) {
                    $sql .= " AND model LIKE ?";
                    $params[] = "%$model%";
                }
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                
                while ($product = $stmt->fetch()):
                ?>
                <tr>
                    <td><?= $product['brand'] ?></td>
                    <td><?= $product['model'] ?></td>
                    <td><?= $product['glass_type'] ?></td>
                    <td>Rs. <?= number_format($product['price'], 2) ?></td>
                    <td>
                        <?php if($product['quantity'] > 10): ?>
                            <span class="badge bg-success">In Stock</span>
                        <?php elseif($product['quantity'] > 0): ?>
                            <span class="badge bg-warning">Low Stock</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Out of Stock</span>
                        <?php endif; ?>
                        (<?= $product['quantity'] ?>)
                    </td>
                    <td>
                        <a href="sales.php?product_id=<?= $product['id'] ?>" class="btn btn-sm btn-success">
                            <i class="fa-solid fa-cart-shopping"></i> Sell
                        </a>
                        <a href="restock.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-primary">
                            <i class="fa-solid fa-boxes-stacked"></i> Restock
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>