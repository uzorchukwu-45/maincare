<?php
include 'header.php';// Ensure DB connection is loaded explicitly

// 1. Security Check
if (!isset($_SESSION['full_name'])) {
    header("Location: login.php");
    exit();
}

// 2. Get the Patient ID safely
if (isset($_SESSION['staff_id'])) {
    $patient_id = $_SESSION['staff_id'];
    $source = "staff_id";
} elseif (isset($_SESSION['patient_id'])) {
    $patient_id = $_SESSION['patient_id'];
    $source = "patient_id";
} elseif (isset($_SESSION['user_id'])) {
    $patient_id = $_SESSION['user_id'];
    $source = "user_id";
} else {
    die("Error: User ID not found in session.");
}

// 3. Fetch Prescriptions
$stmt = $pdo->prepare("SELECT * FROM prescriptions WHERE patient_id = ?");
$stmt->execute([$patient_id]);
$prescriptions = $stmt->fetchAll();

// 4. Include Header (HTML Output starts here)

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DoseCare | Adherence Tracking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">

   
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Adherence Monitoring</h2>
        <a href="nurse_dashboard.php" class="btn btn-outline-secondary">Back to Dashboard</a>
    </div>

    <?php if(isset($_SESSION['success_msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="card-title mb-4">Scheduled Doses for Today</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-danger text-white">
                        <tr>
                            <th>Drug Name</th>
                            <th>Dosage</th>
                            <th>Time</th>
                            <th>Today's Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prescriptions as $drug): ?>
                            <?php 
                                $check_log = $pdo->prepare("SELECT status, recorded_at FROM adherence_records WHERE patient_id = ? AND prescription_id = ? AND DATE(recorded_at) = CURDATE()");
                                $check_log->execute([$patient_id, $drug['id']]); 
                                $today_log = $check_log->fetch();
                            ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($drug['drug_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($drug['dosage_instruction']); ?></td>
                                <td><?php echo htmlspecialchars($drug['dose_time']); ?></td>
                                <td>
                                    <?php if ($today_log): ?>
                                        <?php if ($today_log['status'] == 'Taken'): ?>
                                            <span class="badge bg-success">Taken at <?php echo date('H:i', strtotime($today_log['recorded_at'])); ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Missed</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="d-flex gap-2">
                                            <form action="record_adherence.php" method="POST">
                                                <input type="hidden" name="prescription_id" value="<?php echo $drug['id']; ?>">
                                                <input type="hidden" name="drug_name" value="<?php echo htmlspecialchars($drug['drug_name']); ?>">
                                                <input type="hidden" name="status" value="Taken">
                                                <button type="submit" class="btn btn-success btn-sm">Mark as Taken</button>
                                            </form>
                                            <form action="record_adherence.php" method="POST">
                                                <input type="hidden" name="prescription_id" value="<?php echo $drug['id']; ?>">
                                                <input type="hidden" name="drug_name" value="<?php echo htmlspecialchars($drug['drug_name']); ?>">
                                                <input type="hidden" name="status" value="Missed">
                                                <button type="submit" class="btn btn-outline-danger btn-sm">Missed</button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <?php if (count($prescriptions) == 0): ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">No active prescriptions found for this patient.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php include 'footer.php'; ?>