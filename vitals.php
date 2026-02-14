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
    <title>DoseCare | Update Vital Signs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-secondary text-white py-3">
                    <h4 class="mb-0 text-center">Patient Vital Signs Update</h4>
                </div>
                <div class="card-body p-4">
                    <form action="process_vitals.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Select Patient (ID/Name)</label>
                            <input type="text" name="patient_info" class="form-control" placeholder="Search for outpatient..." required>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Blood Pressure (mmHg)</label>
                                <input type="text" name="bp" class="form-control" placeholder="120/80">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Body Temperature (°C)</label>
                                <input type="number" step="0.1" name="temp" class="form-control" placeholder="36.5">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Heart Rate (BPM)</label>
                                <input type="number" name="heart_rate" class="form-control" placeholder="72">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Weight (kg)</label>
                                <input type="number" step="0.1" name="weight" class="form-control" placeholder="70">
                            </div>
                        </div>

                        <div class="mt-4 mb-4">
                            <label class="form-label">Nurse's Observation Note</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Enter any significant recovery observations..."></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Save Record</button>
                            <a href="nurse_dashboard.php" class="btn btn-link text-muted">Discard Entry</a>
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