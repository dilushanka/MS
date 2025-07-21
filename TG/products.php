<?php
// Database configuration (replace with your actual credentials)
$host = 'localhost';
$db   = 'glass_shop';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_product'])) {
        $brand = $_POST['brand'];
        $model = $_POST['model'];
        $glass_type = $_POST['glass_type'];
        $price = $_POST['price'];
        $quantity = $_POST['quantity'];
        
        $stmt = $pdo->prepare("INSERT INTO products (brand, model, glass_type, price, quantity) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$brand, $model, $glass_type, $price, $quantity]);
    }
    
    if (isset($_POST['update_product'])) {
        $id = $_POST['product_id'];
        $brand = $_POST['brand'];
        $model = $_POST['model'];
        $glass_type = $_POST['glass_type'];
        $price = $_POST['price'];
        $quantity = $_POST['quantity'];
        
        $stmt = $pdo->prepare("UPDATE products SET brand=?, model=?, glass_type=?, price=?, quantity=? WHERE id=?");
        $stmt->execute([$brand, $model, $glass_type, $price, $quantity, $id]);
    }
    
    if (isset($_POST['delete_product'])) {
        $id = $_POST['product_id'];
        $stmt = $pdo->prepare("DELETE FROM products WHERE id=?");
        $stmt->execute([$id]);
    }
}

// Fetch all products
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GlassPro - Product Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Favicon and App Icons -->
<link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
<link rel="manifest" href="favicon/site.webmanifest">
<link rel="icon" href="favicon/favicon.ico" type="image/x-icon">
<meta name="theme-color" content="#ffffff">

    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --light: #f8f9fa;
            --dark: #212529;
            --danger: #e63946;
            --warning: #fca311;
        }
        
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border: none;
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 1.5rem;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }
        
        .card-header {
            border-radius: 12px 12px 0 0 !important;
            background-color: white;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        .table th {
            font-weight: 600;
            color: #5a6169;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table td {
            vertical-align: middle;
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary);
            border-color: var(--secondary);
        }
        
        .btn-sm {
            border-radius: 6px;
            padding: 0.25rem 0.75rem;
        }
        
        .status-badge {
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .badge-stock-high {
            background-color: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }
        
        .badge-stock-medium {
            background-color: rgba(255, 193, 7, 0.15);
            color: #ffc107;
        }
        
        .badge-stock-low {
            background-color: rgba(220, 53, 69, 0.15);
            color: #dc3545;
        }
        
        .search-container {
            position: relative;
        }
        
        .search-container .form-control {
            padding-left: 2.5rem;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        
        .search-container i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        
        .action-btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            margin: 0 2px;
        }
        
        .modal-header {
            background-color: var(--primary);
            color: white;
            border-radius: 12px 12px 0 0 !important;
        }
        
        .modal-title {
            font-weight: 600;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1px solid #dee2e6;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
        }
        
        .stats-card {
            border-left: 4px solid var(--primary);
        }
        
        .stats-card .card-title {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
        }
        
        .stats-card .card-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark);
        }
        
        footer {
            background-color: white;
            padding: 1.5rem 0;
            border-top: 1px solid rgba(0,0,0,0.05);
            margin-top: 2rem;
        }
        
        .product-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            background-color: #eef2ff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fa-solid fa-mobile-screen me-2"></i>GlassPro
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="fa-solid fa-gauge me-1"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="products.php"><i class="fa-solid fa-boxes-stacked me-1"></i> Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sales.php"><i class="fa-solid fa-cash-register me-1"></i> Sales</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="search.php"><i class="fa-solid fa-magnifying-glass me-1"></i> Search</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0"><i class="fa-solid fa-boxes-stacked me-2 text-primary"></i>Product Inventory</h1>
                <p class="text-muted mb-0">Manage your tempered glass products</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="fa-solid fa-plus me-1"></i> Add Product
            </button>
        </div>
        
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title">Total Products</h5>
                                <div class="card-value"><?= count($products) ?></div>
                            </div>
                            <div class="text-primary">
                                <i class="fa-solid fa-box fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title">In Stock</h5>
                                <div class="card-value">
                                    <?php 
                                    $inStock = 0;
                                    foreach($products as $p) {
                                        if($p['quantity'] > 0) $inStock++;
                                    }
                                    echo $inStock;
                                    ?>
                                </div>
                            </div>
                            <div class="text-success">
                                <i class="fa-solid fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title">Low Stock</h5>
                                <div class="card-value">
                                    <?php 
                                    $lowStock = 0;
                                    foreach($products as $p) {
                                        if($p['quantity'] > 0 && $p['quantity'] <= 5) $lowStock++;
                                    }
                                    echo $lowStock;
                                    ?>
                                </div>
                            </div>
                            <div class="text-warning">
                                <i class="fa-solid fa-exclamation-triangle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title">Out of Stock</h5>
                                <div class="card-value">
                                    <?php 
                                    $outOfStock = 0;
                                    foreach($products as $p) {
                                        if($p['quantity'] == 0) $outOfStock++;
                                    }
                                    echo $outOfStock;
                                    ?>
                                </div>
                            </div>
                            <div class="text-danger">
                                <i class="fa-solid fa-times-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Search and Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="search-container">
                            <i class="fa-solid fa-search"></i>
                            <input type="text" class="form-control" id="searchInput" placeholder="Search products by brand, model or type...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex">
                            <select class="form-select me-2" id="stockFilter">
                                <option value="all">All Stock Status</option>
                                <option value="in_stock">In Stock</option>
                                <option value="low_stock">Low Stock</option>
                                <option value="out_of_stock">Out of Stock</option>
                            </select>
                            <button class="btn btn-outline-primary">
                                <i class="fa-solid fa-filter"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Products Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Product List</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="width: 60px;"></th>
                                <th>Product</th>
                                <th>Type</th>
                                <th>Price (LKR)</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="productTableBody">
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <div class="product-image">
                                        <i class="fa-solid fa-mobile-screen"></i>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($product['brand']) ?></div>
                                    <div class="text-muted"><?= htmlspecialchars($product['model']) ?></div>
                                </td>
                                <td><?= htmlspecialchars($product['glass_type']) ?></td>
                                <td>Rs. <?= number_format($product['price'], 2) ?></td>
                                <td><?= $product['quantity'] ?></td>
                                <td>
                                    <?php if($product['quantity'] > 10): ?>
                                        <span class="status-badge badge-stock-high">In Stock</span>
                                    <?php elseif($product['quantity'] > 0): ?>
                                        <span class="status-badge badge-stock-medium">Low Stock</span>
                                    <?php else: ?>
                                        <span class="status-badge badge-stock-low">Out of Stock</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary action-btn edit-product" 
                                        data-id="<?= $product['id'] ?>"
                                        data-brand="<?= $product['brand'] ?>"
                                        data-model="<?= $product['model'] ?>"
                                        data-type="<?= $product['glass_type'] ?>"
                                        data-price="<?= $product['price'] ?>"
                                        data-quantity="<?= $product['quantity'] ?>">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger action-btn delete-product" 
                                        data-id="<?= $product['id'] ?>">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if(empty($products)): ?>
                <div class="text-center py-5">
                    <i class="fa-solid fa-box-open fa-3x text-muted mb-3"></i>
                    <h5>No products found</h5>
                    <p class="text-muted">Add your first product to get started</p>
                    <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="fa-solid fa-plus me-1"></i> Add Product
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="text-center">
                <p class="mb-0">GlassPro Management System &copy; <?= date('Y') ?></p>
                <p class="text-muted small mb-0">Designed for mobile repair shops in Sri Lanka</p>
            </div>
        </div>
    </footer>
    
    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa-solid fa-plus me-2"></i> Add New Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Brand *</label>
                            <input type="text" class="form-control" name="brand" required placeholder="e.g. Apple, Samsung">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Model *</label>
                            <input type="text" class="form-control" name="model" required placeholder="e.g. iPhone 12, Galaxy S21">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Glass Type *</label>
                            <select class="form-select" name="glass_type" required>
                                <option value="">Select type</option>
                                <option value="Tempered Glass">Tempered Glass</option>
                                <option value="Privacy Tempered">Privacy Tempered</option>
                                <option value="Anti-Glare">Anti-Glare</option>
                                <option value="Blue Light Filter">Blue Light Filter</option>
                                <option value="Curved Edge">Curved Edge</option>
                                <option value="Full Coverage">Full Coverage</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Price (LKR) *</label>
                                <input type="number" step="0.01" class="form-control" name="price" required placeholder="0.00">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Quantity *</label>
                                <input type="number" class="form-control" name="quantity" required placeholder="0">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa-solid fa-pen me-2"></i> Edit Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="post">
                    <input type="hidden" name="product_id" id="editProductId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Brand *</label>
                            <input type="text" class="form-control" name="brand" id="editBrand" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Model *</label>
                            <input type="text" class="form-control" name="model" id="editModel" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Glass Type *</label>
                            <select class="form-select" name="glass_type" id="editType" required>
                                <option value="Tempered Glass">Tempered Glass</option>
                                <option value="Privacy Tempered">Privacy Tempered</option>
                                <option value="Anti-Glare">Anti-Glare</option>
                                <option value="Blue Light Filter">Blue Light Filter</option>
                                <option value="Curved Edge">Curved Edge</option>
                                <option value="Full Coverage">Full Coverage</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Price (LKR) *</label>
                                <input type="number" step="0.01" class="form-control" name="price" id="editPrice" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Quantity *</label>
                                <input type="number" class="form-control" name="quantity" id="editQuantity" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_product" class="btn btn-primary">Update Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title"><i class="fa-solid fa-trash me-2"></i> Delete Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="post">
                    <input type="hidden" name="product_id" id="deleteProductId">
                    <div class="modal-body">
                        <p>Are you sure you want to delete this product?</p>
                        <div class="alert alert-warning">
                            <i class="fa-solid fa-exclamation-triangle me-2"></i>
                            This action cannot be undone. All sales records for this product will also be removed.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="delete_product" class="btn btn-danger">Delete Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Edit Product Modal
        document.querySelectorAll('.edit-product').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const brand = this.getAttribute('data-brand');
                const model = this.getAttribute('data-model');
                const type = this.getAttribute('data-type');
                const price = this.getAttribute('data-price');
                const quantity = this.getAttribute('data-quantity');
                
                document.getElementById('editProductId').value = id;
                document.getElementById('editBrand').value = brand;
                document.getElementById('editModel').value = model;
                document.getElementById('editType').value = type;
                document.getElementById('editPrice').value = price;
                document.getElementById('editQuantity').value = quantity;
                
                // Show the modal
                const editModal = new bootstrap.Modal(document.getElementById('editProductModal'));
                editModal.show();
            });
        });
        
        // Delete Product Modal
        document.querySelectorAll('.delete-product').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                document.getElementById('deleteProductId').value = id;
                
                // Show the modal
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteProductModal'));
                deleteModal.show();
            });
        });
        
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#productTableBody tr');
            
            rows.forEach(row => {
                const brand = row.cells[1].querySelector('.fw-bold').textContent.toLowerCase();
                const model = row.cells[1].querySelector('.text-muted').textContent.toLowerCase();
                const type = row.cells[2].textContent.toLowerCase();
                
                if (brand.includes(searchTerm) || model.includes(searchTerm) || type.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // Stock filter
        document.getElementById('stockFilter').addEventListener('change', function() {
            const filterValue = this.value;
            const rows = document.querySelectorAll('#productTableBody tr');
            
            rows.forEach(row => {
                const status = row.cells[5].querySelector('.status-badge').textContent.toLowerCase();
                const statusMap = {
                    'in stock': 'in_stock',
                    'low stock': 'low_stock',
                    'out of stock': 'out_of_stock'
                };
                
                if (filterValue === 'all' || statusMap[status] === filterValue) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>