<?php include 'includes/header.php'; ?>
<?php require_once 'config.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    
    // Update inventory
    $stmt = $pdo->prepare("UPDATE products SET quantity = quantity + ?, last_restocked = CURDATE() WHERE id = ?");
    $stmt->execute([$quantity, $product_id]);
    
    echo '<div class="alert alert-success">Inventory updated successfully!</div>';
}

$product = null;
if(isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch();
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fa-solid fa-boxes-stacked"></i> Restock Inventory</h1>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><?= $product ? "Restock Product" : "Select Product" ?></h5>
            </div>
            <div class="card-body">
                <?php if($product): ?>
                <form method="post">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Product</label>
                        <input type="text" class="form-control" value="<?= $product['brand'] ?> <?= $product['model'] ?>" readonly>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Current Stock</label>
                            <input type="text" class="form-control" value="<?= $product['quantity'] ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Add Quantity</label>
                            <input type="number" name="quantity" class="form-control" min="1" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa-solid fa-save"></i> Update Inventory
                    </button>
                </form>
                <?php else: ?>
                <form method="get" action="restock.php">
                    <div class="mb-3">
                        <label class="form-label">Search Product</label>
                        <input type="text" class="form-control" name="id" placeholder="Enter product ID">
                    </div>
                    <p class="text-center">OR</p>
                    <div class="d-grid">
                        <a href="products.php" class="btn btn-outline-primary">
                            <i class="fa-solid fa-boxes-stacked"></i> View Full Inventory
                        </a>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>