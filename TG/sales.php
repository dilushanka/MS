<?php include 'includes/header.php'; ?>
<?php require_once 'config.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    
    // Record sale
    $stmt = $pdo->prepare("INSERT INTO sales (product_id, quantity_sold) VALUES (?, ?)");
    $stmt->execute([$product_id, $quantity]);
    
    // Update inventory
    $stmt = $pdo->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
    $stmt->execute([$quantity, $product_id]);
    
    echo '<div class="alert alert-success">Sale recorded successfully!</div>';
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fa-solid fa-cash-register"></i> Sell Glass</h1>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5>Select Product</h5>
            </div>
            <div class="card-body">
                <form method="get" action="sales.php">
                    <div class="mb-3">
                        <label class="form-label">Search Product</label>
                        <input type="text" class="form-control" name="search" placeholder="Enter model or brand">
                    </div>
                    <button type="submit" class="btn btn-primary">Find Product</button>
                </form>
                
                <?php if(isset($_GET['search'])): ?>
                <hr>
                <h6>Search Results:</h6>
                <div class="list-group">
                    <?php
                    $search = '%'.$_GET['search'].'%';
                    $stmt = $pdo->prepare("SELECT * FROM products WHERE model LIKE ? OR brand LIKE ?");
                    $stmt->execute([$search, $search]);
                    
                    while ($product = $stmt->fetch()):
                    ?>
                    <a href="sales.php?product_id=<?= $product['id'] ?>" 
                       class="list-group-item list-group-item-action">
                       <div class="d-flex w-100 justify-content-between">
                           <h6 class="mb-1"><?= $product['brand'] ?> <?= $product['model'] ?></h6>
                           <small>Rs. <?= number_format($product['price'], 2) ?></small>
                       </div>
                       <small>Stock: <?= $product['quantity'] ?></small>
                    </a>
                    <?php endwhile; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <?php if(isset($_GET['product_id'])): 
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$_GET['product_id']]);
            $product = $stmt->fetch();
        ?>
        <div class="card">
            <div class="card-header">
                <h5>Complete Sale</h5>
            </div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Product</label>
                        <input type="text" class="form-control" value="<?= $product['brand'] ?> <?= $product['model'] ?>" readonly>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Price per Unit</label>
                            <input type="text" class="form-control" value="Rs. <?= number_format($product['price'], 2) ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Current Stock</label>
                            <input type="text" class="form-control" value="<?= $product['quantity'] ?>" readonly>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Quantity to Sell</label>
                        <input type="number" name="quantity" class="form-control" min="1" max="<?= $product['quantity'] ?>" required>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fa-solid fa-check-circle"></i> Confirm Sale
                    </button>
                </form>
            </div>
        </div>
        <?php else: ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fa-solid fa-mobile-screen fa-4x mb-3 text-muted"></i>
                <h5>Select a product to sell</h5>
                <p class="text-muted">Search for a product or select from inventory</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>