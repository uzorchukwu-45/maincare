<?php


include 'header.php';
if ($_SESSION['user_role'] !== 'Pharmacist') { header("Location: pharmacist_dashboard.php"); exit(); }

?>
<div class="container mt-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold">Pharmacist Dashboard</h2>
        <p class="lead">Welcome, Pharm. <?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
    </div>

    <div class="row justify-content-center g-4">
        
        <div class="col-md-5 col-lg-4">
            <div class="card bg-primary text-white p-4 shadow-sm h-100 border-0">
                <h4 class="fw-bold">Pending Dispensing</h4>
                <p>Check prescriptions ready for collection.</p>
                <a href="dispense.php" class="btn btn-light mt-auto">View Queue</a>
            </div>
        </div>

        <div class="col-md-5 col-lg-4">
            <div class="card bg-primary text-white p-4 shadow-sm h-100 border-0">
                <h4 class="fw-bold">Drug Inventory</h4>
                <p>Manage stock levels for outpatients.</p>
                <a href="inventory.php" class="btn btn-light mt-auto">Manage</a>
            </div>
        </div>

    </div>
</div>
<?php include 'footer.php'; ?>