<?php
include 'header.php';

// Sync variable names: Using $patient_input to capture what comes from the URL
$patient_input = $_GET['patient_id'] ?? $_POST['patient_id'] ?? null;
$rx_data = []; 

if ($patient_input) {
    try {
        // 1. Fetch Patient Info searching BOTH the numeric 'id' and the string 'patient_id'
        // This ensures that whether '105' or 'PLH-105' is passed, the record is found.
        $stmtP = $pdo->prepare("SELECT id, patient_id, name FROM patients WHERE id = ? OR patient_id = ?");
        $stmtP->execute([$patient_input, $patient_input]);
        $patient = $stmtP->fetch();

        if ($patient) {
            // Synchronize the specific ID variables for use in the rest of the script
            $patient_id_str = $patient['patient_id']; // The string e.g., PLH-105
            $numeric_id = $patient['id'];             // The numeric PK e.g., 105

            // 2. Fetch Latest Prescription using the confirmed numeric Primary Key
            $stmtRx = $pdo->prepare("SELECT * FROM prescriptions WHERE patient_id = ? ORDER BY id DESC LIMIT 1");
            $stmtRx->execute([$numeric_id]);
            $rx_data = $stmtRx->fetch();
        } else {
            die("<div class='alert alert-danger'>Error: Patient record not found for ID: " . htmlspecialchars($patient_input) . "</div>");
        }
    } catch (PDOException $e) {
        die("Query Failed: " . $e->getMessage());
    }
} else {
    header("Location: view_patients.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pid_str = $_POST['patient_id']; // The string (PLH-102)
    $rx_id = $_POST['rx_id'];       // The numeric PK of the prescription
    $medication = $_POST['medication_name'];
    $dosage = $_POST['dosage'];
    $frequency = $_POST['frequency'];
    $duration = $_POST['duration']; 
    $instructions = $_POST['instructions'];

    if (empty($medication) || empty($dosage)) {
        $error = "Please fill in required fields.";
    } else {
        try {
            // FIX: Get the REAL numeric 'id' from the patients table to satisfy the Foreign Key
            $stmtCheck = $pdo->prepare("SELECT id FROM patients WHERE patient_id = ?");
            $stmtCheck->execute([$pid_str]);
            $patient_row = $stmtCheck->fetch();

            if (!$patient_row) {
                die("Error: Patient record not found for ID: " . htmlspecialchars($pid_str));
            }

            $numeric_patient_id = $patient_row['id'];

            if (!empty($rx_id)) {
                // UPDATE existing record
                $sql = "UPDATE prescriptions SET medication_name=?, dosage=?, frequency=?, duration=?, instructions=? WHERE id=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$medication, $dosage, $frequency, $duration, $instructions, $rx_id]);
            } else {
                // INSERT new record using the numeric ID
                $sql = "INSERT INTO prescriptions (patient_id, medication_name, dosage, frequency, duration, instructions) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$numeric_patient_id, $medication, $dosage, $frequency, $duration, $instructions]);
            }

            header("Location: patient_details.php?id=" . urlencode($pid_str) . "&msg=rx_updated");
            exit();

        } catch (PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Prescription - DoseCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white fw-bold">
                        Update Prescription for: <?php echo htmlspecialchars($patient['name'] ?? 'Patient'); ?>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <input type="hidden" name="patient_id" value="<?php echo htmlspecialchars($patient_id_str); ?>">
                            <input type="hidden" name="rx_id" value="<?php echo htmlspecialchars($rx_data['id'] ?? ''); ?>">

                            <div class="mb-3">
                                <label class="form-label">Medication Name</label>
                                <input type="text" name="medication_name" class="form-control" value="<?php echo htmlspecialchars($rx_data['medication_name'] ?? ''); ?>" required>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Dosage</label>
                                    <input type="text" name="dosage" class="form-control" value="<?php echo htmlspecialchars($rx_data['dosage'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Frequency</label>
                                    <select name="frequency" class="form-select" required>
                                        <?php $freq = $rx_data['frequency'] ?? ''; ?>
                                        <option value="">Select...</option>
                                        <option value="Once Daily" <?php if($freq == 'Once Daily') echo 'selected'; ?>>Once Daily</option>
                                        <option value="Twice Daily" <?php if($freq == 'Twice Daily') echo 'selected'; ?>>Twice Daily</option>
                                        <option value="Thrice Daily" <?php if($freq == 'Thrice Daily') echo 'selected'; ?>>Thrice Daily</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Duration</label>
                                    <input type="text" name="duration" class="form-control" placeholder="e.g. 1 Month" value="<?php echo htmlspecialchars($rx_data['duration'] ?? ''); ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Special Instructions</label>
                                <textarea name="instructions" class="form-control" rows="3"><?php echo htmlspecialchars($rx_data['instructions'] ?? ''); ?></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="patient_details.php?id=<?php echo urlencode($patient_id_str); ?>" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary" style="background-color: #565e8b; border: none;">Save Changes</button>
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