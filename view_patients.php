<?php
include 'header.php';



// Include your database connection file (e.g., config.php or db.php)
// include 'db_connect.php'; 

if (!isset($_SESSION['full_name'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DoseCare | View Patients</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Outpatient Adherence List</h2>
        <a href="doctor_dashboard.php" class="btn btn-outline-secondary">Back to Dashboard</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-primary">
                    <tr>
                        <th>Patient ID</th>
                        <th>Name</th>
                        <th>Condition</th>
                        <th>Adherence Rate</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // 1. Fetch all patients from the database
                    // Ensure $pdo is available (it usually comes from header.php -> config.php)
                    if (isset($pdo)) {
                        $stmt = $pdo->query("SELECT * FROM patients ORDER BY id DESC");
                        $patients = $stmt->fetchAll();

                        if (count($patients) > 0) {
                            foreach ($patients as $row) {
                                // 2. Calculate Adherence Rate for each patient
                                // We look up logs for this specific patient ID
                                $logStmt = $pdo->prepare("SELECT status FROM adherence_logs WHERE patient_id = ?");
                                $logStmt->execute([$row['id']]);
                                $logs = $logStmt->fetchAll();

                                $total_logs = count($logs);
                                $taken_count = 0;
                                foreach ($logs as $log) {
                                    if (strcasecmp($log['status'], 'Taken') == 0 || strcasecmp($log['status'], 'YES') == 0) {
                                        $taken_count++;
                                    }
                                }
                                
                                // Calculate percentage (avoid division by zero)
                                $rate = ($total_logs > 0) ? round(($taken_count / $total_logs) * 100) : 0;

                                // 3. Determine Color and Status Label
                                $bar_color = 'bg-success';
                                $status_badge = 'bg-success';
                                $status_text = 'Stable';

                                if ($rate < 50) {
                                    $bar_color = 'bg-danger';
                                    $status_badge = 'bg-danger';
                                    $status_text = 'Critical';
                                } elseif ($rate < 80) {
                                    $bar_color = 'bg-warning text-dark';
                                    $status_badge = 'bg-warning text-dark';
                                    $status_text = 'Watch';
                                }
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['patient_id']); ?></td>
                                    
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    
                                    <td><?php echo htmlspecialchars($row['condition_name'] ?? $row['condition'] ?? 'N/A'); ?></td>
                                    
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1" style="height: 20px;">
                                                <div class="progress-bar <?php echo $bar_color; ?>" role="progressbar" style="width: <?php echo $rate; ?>%;">
                                                    <?php echo $rate; ?>%
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td><span class="badge <?php echo $status_badge; ?>"><?php echo $status_text; ?></span></td>
                                    
                                    <td>
                                        <a href="patient_details.php?id=<?php echo $row['patient_id']; ?>" class="btn btn-sm btn-primary">
                                            View History
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            // If no patients exist
                            echo "<tr><td colspan='6' class='text-center'>No patients found.</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center text-danger'>Database connection error.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>