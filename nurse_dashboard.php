<?php
include 'header.php';


if ($_SESSION['user_role'] !== 'Nurse') { header("Location: nurse_dashboard.php"); exit(); }

?>
<div class="container mt-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold">Nurse Dashboard</h2>
        <p class="lead text-muted">Welcome, Nurse <?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
    </div>

    <div class="row justify-content-center g-4">
        
        <div class="col-md-5 col-lg-4">
            <div class="card bg-danger text-white p-4 shadow-sm h-100 border-0">
                <h4 class="fw-bold">Adherence Monitoring</h4>
                <p>Track if patients are taking medication on time.</p>
                <a href="track_adherence.php" class="btn btn-light mt-auto fw-bold">Track</a>
            </div>
        </div>

        <div class="col-md-5 col-lg-4">
            <div class="card bg-secondary text-white p-4 shadow-sm h-100 border-0">
                <h4 class="fw-bold">Vital Signs</h4>
                <p>Update patient recovery status.</p>
                <a href="vitals.php" class="btn btn-light mt-auto fw-bold">Update</a>
            </div>
        </div>

    </div>
</div>
<?php include 'footer.php'; ?>