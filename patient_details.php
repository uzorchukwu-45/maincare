<?php
include 'header.php';

// ---------------------------------------------------------
// NEW: Fetch All Patients for the Dropdown Menu
// ---------------------------------------------------------
try {
    $allPatientsStmt = $pdo->query("SELECT patient_id, name FROM patients ORDER BY name ASC");
    $all_patients_list = $allPatientsStmt->fetchAll();
} catch (PDOException $e) {
    // Silent fail if list cannot be fetched, dropdown will just be empty
    $all_patients_list = [];
}
// ---------------------------------------------------------

if (isset($_GET['id'])) {
    $patient_input = $_GET['id']; 

    try {
        // 1. Fetch Patient Info (Searching both ID types to be safe)
        $stmt = $pdo->prepare("SELECT id, patient_id, name, age, phone, condition_name FROM patients WHERE patient_id = ? OR id = ?");
        $stmt->execute([$patient_input, $patient_input]);
        $patient = $stmt->fetch();

        if (!$patient) {
            die("<div class='alert alert-danger'>Patient [$patient_input] not found.</div>");
        }

        // SYNC IDs: 
        $real_numeric_id = $patient['id']; // This is 105
        $patient_id_str = $patient['patient_id']; // This is PLH-105

        // 2. Fetch Prescriptions using the NUMERIC ID
        $stmtRx = $pdo->prepare("SELECT * FROM prescriptions WHERE patient_id = ?");
        $stmtRx->execute([$real_numeric_id]);
        $prescriptions = $stmtRx->fetchAll();

        // 3. Fetch Logs using the NUMERIC ID
        $stmtLogs = $pdo->prepare("SELECT * FROM adherence_logs WHERE patient_id = ? ORDER BY logged_at DESC");
        $stmtLogs->execute([$real_numeric_id]);
        $logs = $stmtLogs->fetchAll();

        // --- CALCULATION LOGIC ---
        $total_logs = count($logs);
        $taken_count = 0;
        foreach ($logs as $log) {
            if (strcasecmp($log['status'], 'Taken') == 0 || strcasecmp($log['status'], 'YES') == 0) {
                $taken_count++;
            }
        }
        $adherence_rate = ($total_logs > 0) ? round(($taken_count / $total_logs) * 100) : 0;

        $badge_color = 'bg-success';
        if ($adherence_rate < 50) $badge_color = 'bg-danger';
        elseif ($adherence_rate < 80) $badge_color = 'bg-warning text-dark';

    } catch (PDOException $e) {
        die("Query Failed: " . $e->getMessage());
    }
} else {
    header("Location: view_patients.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Details - DoseCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar-custom { background-color: #565e8b; color: white; }
        .card-header-custom { background-color: #e9ecef; font-weight: bold; color: #565e8b; }
        .adherence-circle {
            width: 120px; height: 120px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem; font-weight: bold; color: white; margin: 0 auto;
        }
        .d-flex{
            background-color: #565e8b !important; /* Blue Background */
            color: white !important;
        }

        /* --- NEW STYLES FOR BLUE DROPDOWN AND BUTTON --- */
        
        /* 1. The Dropdown Menu Box */
        .custom-blue-dropdown {
            background-color: #565e8b !important; /* Blue Background */
            border: 1px solid white;
        }

        /* 2. The Dropdown Items (Links) */
        .custom-blue-dropdown .dropdown-item {
            color: white !important; /* White Text */
        }
        .custom-blue-dropdown .dropdown-item:hover {
            background-color: #454b6f !important; /* Slightly darker blue on hover */
            color: white !important;
        }
        
        /* 3. The Headers/Dividers inside dropdown */
        .custom-blue-dropdown .dropdown-header {
            color: #d1d5db !important; /* Light grey text for headers */
        }
        .custom-blue-dropdown .dropdown-divider {
            border-top: 1px solid rgba(255,255,255,0.3);
        }

        /* 4. The Back to Dashboard Button */
        .btn-custom-blue {
            background-color: #565e8b !important;
            color: white !important;
            border: 1px solid white !important;
        }
        .btn-custom-blue:hover {
            background-color: white !important;
            color: #565e8b !important;
        }

        /* 5. Special Style for Broadcast */
        .broadcast-item {
            background-color: #4a90e2 !important; /* Brighter Blue for emphasis */
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-custom mb-4">
        <div class="container">
            <a class="navbar-brand text-white fw-bold" href="#">DoseCare</a>
            
            <div class="d-flex align-items-center">
                <div class="dropdown me-3">
                    <button class="btn btn-outline-light dropdown-toggle btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo htmlspecialchars($patient['name']); ?>
                    </button>
                    
                    <ul class="dropdown-menu custom-blue-dropdown" style="max-height: 300px; overflow-y: auto;">
                        
                        <li>
                            <a class="dropdown-item broadcast-item" href="#" onclick="confirmBroadcast('<?php echo $patient['patient_id']; ?>'); return false;">
                                📢 Broadcast to All Users
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        
                        <li><h6 class="dropdown-header">Switch to...</h6></li>
                        <?php foreach ($all_patients_list as $p): ?>
                            <li>
                                <a class="dropdown-item" href="patient_details.php?id=<?php echo $p['patient_id']; ?>">
                                    <?php echo htmlspecialchars($p['name']); ?> 
                                    <small>(<?php echo htmlspecialchars($p['patient_id']); ?>)</small>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <a href="view_patients.php" class="btn btn-sm btn-custom-blue">Back to Dashboard</a>
            </div>
        </div>
    </nav>

    <?php if (isset($_GET['msg'])): ?>
    <div class="container mt-3">
        <?php if ($_GET['msg'] == 'sms_sent'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> Medication reminder sent to <?php echo htmlspecialchars($patient['name']); ?>.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($_GET['msg'] == 'broadcast_sent'): ?>
             <div class="alert alert-primary alert-dismissible fade show" role="alert">
                <strong>Broadcast Sent!</strong> Reminders have been sent to ALL patients.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($_GET['msg'] == 'manual_sent'): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <strong>Sent!</strong> Internal reminder logged for <?php echo htmlspecialchars($patient['name']); ?>.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($_GET['msg'] == 'issue_reported'): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Reported!</strong> The issue has been logged successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($_GET['msg'] == 'dose_logged'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Logged!</strong> Adherence status updated successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($_GET['msg'] == 'rx_updated'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Updated!</strong> Prescription details saved.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($_GET['msg'] == 'no_rx'): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Failed:</strong> This patient has no active prescription to remind them about.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="container">
        <div class="row mb-3 align-items-center">
            <div class="col-md-6">
                <h3>Patient Profile: <?php echo htmlspecialchars($patient['name']); ?></h3>
                <p class="text-muted">ID: PLH-<?php echo htmlspecialchars($patient['patient_id']); ?></p>
            </div>
            
            <div class="col-md-6 text-end">
                <div class="btn-group me-2">
                    <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        Log Dose
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <form action="log_dose.php" method="POST">
                                <input type="hidden" name="patient_id" value="<?php echo $patient['id']; ?>">
                                <input type="hidden" name="status" value="Taken">
                                <button type="submit" class="dropdown-item">✅ Mark as Taken</button>
                            </form>
                        </li>
                        <li>
                            <form action="log_dose.php" method="POST">
                                <input type="hidden" name="patient_id" value="<?php echo $patient['id']; ?>">
                                <input type="hidden" name="status" value="Missed">
                                <button type="submit" class="dropdown-item">❌ Mark as Missed</button>
                            </form>
                        </li>
                    </ul>
                </div>
                
                <form action="send_manual_reminder.php" method="GET" style="display: inline;">
                    <input type="hidden" name="id" value="<?php echo $patient['patient_id']; ?>">
                    <button type="submit" class="btn btn-primary">
                        Send Reminder (SMS)
                    </button>
                </form>

                <button type="button" class="btn btn-outline-danger" style="margin-left: 5px;" data-bs-toggle="modal" data-bs-target="#reportIssueModal">
                  Report Issue
                </button>
            </div>
        </div>

        <div class="modal fade" id="reportIssueModal" tabindex="-1" aria-labelledby="reportIssueLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <form action="report_issue.php" method="POST">
                  <div class="modal-header">
                    <h5 class="modal-title" id="reportIssueLabel">Report Issue for <?php echo htmlspecialchars($patient['name']); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <input type="hidden" name="patient_id" value="<?php echo $patient['id']; ?>">
                    
                    <div class="mb-3">
                        <label for="issue_type" class="form-label">Issue Type</label>
                        <select class="form-select" name="issue_type" required>
                            <option value="">Select an Issue...</option>
                            <option value="Non-Adherence">Repeatedly Missed Doses</option>
                            <option value="Side Effects">Side Effects Reported</option>
                            <option value="Lost Medication">Lost Medication</option>
                            <option value="Wrong Dosage">Wrong Dosage Taken</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description / Notes</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Enter details here..." required></textarea>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Submit Report</button>
                  </div>
              </form>
            </div>
          </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header card-header-custom">Personal Information</div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Condition:</strong> <?php echo htmlspecialchars($patient['condition_name'] ?? $patient['condition'] ?? 'N/A'); ?></li>
                            <li class="list-group-item"><strong>Phone:</strong> <?php echo htmlspecialchars($patient['phone']); ?></li>
                            <li class="list-group-item"><strong>Age:</strong> <?php echo htmlspecialchars($patient['age']); ?></li>
                            <li class="list-group-item"><strong>Status:</strong> <span class="badge bg-success">Active</span></li>
                        </ul>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header card-header-custom">Overall Adherence</div>
                    <div class="card-body text-center">
                        <div class="adherence-circle <?php echo $badge_color; ?> mb-3">
                            <?php echo $adherence_rate; ?>%
                        </div>
                        <p>Total Doses Tracked: <strong><?php echo $total_logs; ?></strong></p>
                        <p>Doses Taken: <strong><?php echo $taken_count; ?></strong></p>
                        <div class="progress mt-2" style="height: 10px;">
                            <div class="progress-bar <?php echo $badge_color; ?>" role="progressbar" style="width: <?php echo $adherence_rate; ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header card-header-custom d-flex justify-content-between align-items-center">
                        <span>Current Prescriptions</span>
                        <a href="edit_prescription.php?patient_id=<?php echo $patient['id']; ?>" class="btn btn-sm btn-outline-secondary">Update Prescription</a>
                    </div>
                    <div class="card-body">
                        <?php if (count($prescriptions) > 0): ?>
                        <table class="table table-hover">
                            <thead>
                                <tr><th>Medication</th><th>Dosage</th><th>Frequency</th><th>Duration</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($prescriptions as $rx): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($rx['medication_name']); ?></td>
                                    <td><?php echo htmlspecialchars($rx['dosage']); ?></td>
                                    <td><?php echo htmlspecialchars($rx['frequency']); ?></td>
                                    <td><?php echo htmlspecialchars($rx['duration']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                            <p class="text-muted">No active prescriptions found.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header card-header-custom">Adherence History Log</div>
                    <div class="card-body">
                        <table class="table table-striped table-sm">
                            <thead><tr><th>Date & Time</th><th>Status</th><th>Method</th></tr></thead>
                            <tbody>
                                <?php if (count($logs) > 0): ?>
                                    <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td><?php echo date('M d, Y h:i A', strtotime($log['logged_at'])); ?></td>
                                        <td>
                                            <?php if (strcasecmp($log['status'], 'Taken') == 0): ?>
                                                <span class="badge bg-success">Taken</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Missed</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-muted small">Via Manual Log</td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="text-center">No history recorded yet.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmBroadcast(currentPatientId) {
            if (confirm("⚠️ SYSTEM WARNING:\n\nYou are about to send a reminder to ALL users.\n\nAre you sure you want to proceed?")) {
                window.location.href = "send_manual_reminder.php?mode=broadcast&return_id=" + currentPatientId;
            }
        }
    </script>
</body>
</html>