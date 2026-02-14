<?php
include 'header.php';

if (!isset($_SESSION['full_name'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DoseCare | New Prescription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8"> <div class="card shadow border-0">
                <div class="card-header bg-success text-white text-center py-3">
                    <h4 class="mb-0">Create Adherence Plan</h4>
                </div>
                <div class="card-body p-4">
                    <form action="process_prescription.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Patient Name/ID</label>
                            <input type="text" name="patient_id" class="form-control" placeholder="Search Patient..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Medication Name</label>
                            <input type="text" name="medication_name" class="form-control" placeholder="e.g. Amlodipine" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Dosage (mg)</label>
                                <input type="number" name="dosage" class="form-control" placeholder="5">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Frequency</label>
                                <select name="frequency" class="form-select">
                                    <option value="once">Once Daily</option>
                                    <option value="twice">Twice Daily</option>
                                    <option value="thrice">Three Times Daily</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Duration (Days)</label>
                                <input type="number" name="duration" class="form-control" placeholder="e.g. 7" required>
                            </div>
                        </div>
                 

                        <div class="mb-4">
                            <label class="form-label">Special Instructions</label>
                            <textarea name="instructions" class="form-control" rows="2" placeholder="Take after food..."></textarea>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">Activate Reminder Plan</button>
                            <a href="doctor_dashboard.php" class="btn btn-link text-muted text-decoration-none text-center">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
<?php include 'footer.php'; ?>