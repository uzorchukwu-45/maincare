<?php
include 'header.php';

if ($_SESSION['user_role'] !== 'Doctor') { header("Location: doctor_dashboard.php"); exit(); }

?>
<div class="container mt-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold">Doctor Dashboard</h2>
        <p class="lead text-muted">Welcome, Dr. <?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
    </div>

    <div class="row justify-content-center g-4">
        
        <div class="col-md-5 col-lg-4">
            <div class="card bg-primary text-white p-4 shadow-sm h-100 border-0">
                <div class="card-body d-flex flex-column text-center">
                    <h4 class="fw-bold">Patient List</h4>
                    <p>View and manage outpatient records.</p>
                    <a href="view_patients.php" class="btn btn-light mt-auto fw-bold">Open</a>
                </div>
            </div>
        </div>

        <div class="col-md-5 col-lg-4">
            <div class="card bg-success text-white p-4 shadow-sm h-100 border-0">
                <div class="card-body d-flex flex-column text-center">
                    <h4 class="fw-bold">Prescriptions</h4>
                    <p>Write new medication adherence plans.</p>
                    <a href="add_prescription.php" class="btn btn-light mt-auto fw-bold">Write</a>
                </div>
            </div>
        </div>

    </div>
</div>
<?php include 'footer.php'; ?>