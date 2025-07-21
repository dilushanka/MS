<?php include 'includes/header.php'; ?>
<?php require_once 'config.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fa-solid fa-gauge"></i> Dashboard</h1>
</div>

<div class="row">
    <!-- Summary Cards -->
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-primary h-100">
            <div class="card-body">
                <h5 class="card-title">Total Products</h5>
                <?php
                $stmt = $pdo->query("SELECT COUNT(*) FROM products");
                $count = $stmt->fetchColumn();
                ?>
                <h2 class="card-text"><?= $count ?></h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-success h-100">
            <div class="card-body">
                <h5 class="card-title">Today's Sales</h5>
                <?php
                $stmt = $pdo->query("SELECT SUM(quantity_sold) FROM sales WHERE DATE(sale_date) = CURDATE()");
                $sales = $stmt->fetchColumn() ?: 0;
                ?>
                <h2 class="card-text"><?= $sales ?></h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-warning h-100">
            <div class="card-body">
                <h5 class="card-title">Low Stock Items</h5>
                <?php
                $stmt = $pdo->query("SELECT COUNT(*) FROM products WHERE quantity < 5");
                $lowStock = $stmt->fetchColumn();
                ?>
                <h2 class="card-text"><?= $lowStock ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Recent Sales</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT p.brand, p.model, s.quantity_sold, s.sale_date 
                                            FROM sales s 
                                            JOIN products p ON s.product_id = p.id 
                                            ORDER BY s.sale_date DESC LIMIT 5");
                        while ($row = $stmt->fetch()):
                        ?>
                        <tr>
                            <td><?= $row['brand'] ?> <?= $row['model'] ?></td>
                            <td><?= $row['quantity_sold'] ?></td>
                            <td><?= date('h:i A', strtotime($row['sale_date'])) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Low Stock Alert</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Stock</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT id, brand, model, quantity FROM products WHERE quantity < 5 ORDER BY quantity ASC LIMIT 5");
                        while ($row = $stmt->fetch()):
                        ?>
                        <tr>
                            <td><?= $row['brand'] ?> <?= $row['model'] ?></td>
                            <td><span class="badge bg-danger"><?= $row['quantity'] ?></span></td>
                            <td>
                                <a href="restock.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">
                                    Restock
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>