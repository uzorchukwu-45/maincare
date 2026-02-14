<?php
include 'header.php';

// Security Check: Ensure user is logged in
if (!isset($_SESSION['full_name'])) {
    header("Location: login.php");
    exit();
}

// --- HANDLE FORM SUBMISSIONS ---

// 1. Add New Drug
if (isset($_POST['add_drug'])) {
    $name = $_POST['drug_name'];
    $category = $_POST['category'];
    $form = $_POST['dosage_form'];
    $strength = $_POST['strength'];
    $qty = $_POST['quantity'];
    $expiry = $_POST['expiry_date'];

    try {
        $stmt = $pdo->prepare("INSERT INTO drugs (drug_name, category, dosage_form, strength, quantity, expiry_date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $category, $form, $strength, $qty, $expiry]);
        $_SESSION['success_msg'] = "New drug added successfully!";
    } catch (PDOException $e) {
        $_SESSION['error_msg'] = "Error: " . $e->getMessage();
    }
    // Redirect back to THIS page
    header("Location: inventory.php");
    exit();
}

// 2. Update Stock (Add Quantity)
if (isset($_POST['update_stock'])) {
    $id = $_POST['drug_id'];
    $added_qty = $_POST['added_quantity'];

    try {
        $stmt = $pdo->prepare("UPDATE drugs SET quantity = quantity + ? WHERE id = ?");
        $stmt->execute([$added_qty, $id]);
        $_SESSION['success_msg'] = "Stock updated successfully!";
    } catch (PDOException $e) {
        $_SESSION['error_msg'] = "Error: " . $e->getMessage();
    }
    header("Location: inventory.php");
    exit();
}

// --- FETCH INVENTORY ---
$stmt = $pdo->query("SELECT * FROM drugs ORDER BY drug_name ASC");
$drugs = $stmt->fetchAll();

// Optional: keep if you use a specific header file
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DoseCare | Drug Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Drug Inventory Management</h2>
        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDrugModal">
                + Add New Drug
            </button>
            <a href="pharmacist_dashboard.php" class="btn btn-outline-secondary">Back to Dashboard</a>
        </div>
    </div>

    <?php if(isset($_SESSION['success_msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if(isset($_SESSION['error_msg'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle table-striped">
                    <thead class="table-warning">
                        <tr>
                            <th>Drug Name</th>
                            <th>Category</th>
                            <th>Form & Strength</th>
                            <th>Stock Level</th>
                            <th>Expiry Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($drugs as $d): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($d['drug_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($d['category']); ?></td>
                            <td><?php echo htmlspecialchars($d['dosage_form'] . ' (' . $d['strength'] . ')'); ?></td>
                            <td>
                                <?php if ($d['quantity'] < 10): ?>
                                    <span class="badge bg-danger"><?php echo $d['quantity']; ?> (Low Stock)</span>
                                <?php else: ?>
                                    <span class="badge bg-success"><?php echo $d['quantity']; ?> In Stock</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($d['expiry_date']); ?></td>
                            <td>
                                <button class="btn btn-sm btn-dark" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#updateStockModal"
                                        onclick="setUpdateId(<?php echo $d['id']; ?>, '<?php echo htmlspecialchars($d['drug_name']); ?>')">
                                    Update Stock
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (count($drugs) == 0): ?>
                            <tr><td colspan="6" class="text-center py-4">No drugs found. Click "Add New Drug" to start.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addDrugModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Medication</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Drug Name</label>
                        <input type="text" name="drug_name" class="form-control" placeholder="e.g. Paracetamol" required>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6 mb-3">
                            <label>Category</label>
                            <select name="category" class="form-select">
                                <option>Antibiotic</option>
                                <option>Analgesic</option>
                                <option>Antimalarial</option>
                                <option>Supplement</option>
                                <option>Cardiovascular</option>
                                <option>Hypertension</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Dosage Form</label>
                            <select name="dosage_form" class="form-select">
                                <option>Tablet</option>
                                <option>Syrup</option>
                                <option>Injection</option>
                                <option>Capsule</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6 mb-3">
                            <label>Strength (e.g. 500mg)</label>
                            <input type="text" name="strength" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Initial Quantity</label>
                            <input type="number" name="quantity" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Expiry Date</label>
                        <input type="date" name="expiry_date" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add_drug" class="btn btn-primary">Save Drug</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="updateStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Stock: <span id="drugNameDisplay"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="drug_id" id="updateDrugId">
                    
                    <div class="mb-3">
                        <label>Quantity to ADD</label>
                        <input type="number" name="added_quantity" class="form-control" placeholder="Enter amount to add" required>
                        <small class="text-muted">Tip: Enter a negative number (e.g. -5) to reduce stock.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="update_stock" class="btn btn-dark">Update Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // This script passes the Drug ID to the pop-up modal
    function setUpdateId(id, name) {
        document.getElementById('updateDrugId').value = id;
        document.getElementById('drugNameDisplay').innerText = name;
    }
</script>
</body>
</html>
<?php include 'footer.php'; ?>