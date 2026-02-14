<?php
include 'header.php'; 

// Security Check
if (!isset($_SESSION['full_name'])) {
    header("Location: login.php");
    exit();
}

// --- HANDLE ACTION: MARK AS DISPENSED ---
if (isset($_POST['dispense_id'])) {
    $id = $_POST['dispense_id'];
    
    try {
        // Update status to 'Dispensed' and set the current time
        $stmt = $pdo->prepare("UPDATE dispensing_queue SET status = 'Dispensed', dispensed_at = NOW() WHERE id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['success_msg'] = "Medication dispensed successfully.";
    } catch (PDOException $e) {
        $_SESSION['error_msg'] = "Error: " . $e->getMessage();
    }
    
    // Refresh page to show updated list
    header("Location: dispense.php");
    exit();
}

// --- FETCH PENDING QUEUE ---
// Only select items where status is 'Pending'
$stmt = $pdo->query("SELECT * FROM dispensing_queue WHERE status = 'Pending' ORDER BY id ASC");
$queue = $stmt->fetchAll();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DoseCare | Dispensing Queue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Prescription Dispensing Queue</h2>
        <a href="pharmacist_dashboard.php" class="btn btn-outline-secondary">Back to Dashboard</a>
    </div>

    <?php if(isset($_SESSION['success_msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-info">
                        <tr>
                            <th>Patient ID</th>
                            <th>Patient Name</th>
                            <th>Medication</th>
                            <th>Dosage Plan</th>
                            <th>Doctor</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($queue as $row): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['patient_id']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['medication']); ?></td>
                            <td><?php echo htmlspecialchars($row['dosage_plan']); ?></td>
                            <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                            <td>
                                <form method="POST" onsubmit="return confirm('Confirm dispensing for <?php echo $row['patient_name']; ?>?');">
                                    <input type="hidden" name="dispense_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-success px-3">Mark as Dispensed</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                        <?php if (count($queue) == 0): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <h4> All Clear</h4>
                                    <p>No pending prescriptions in the queue.</p>
                                </td>
                            </tr>
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