<?php
session_start();
require_once 'config.php';

// Check if an ID is provided
if (isset($_GET['id'])) {
    $patient_input = $_GET['id'];

    try {
        // 1. Get Patient Details from 'patients' table
        $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ? OR patient_id = ?");
        $stmt->execute([$patient_input, $patient_input]);
        $patient = $stmt->fetch();

        if ($patient) {
            // Get the phone number exactly as is, just remove accidental spaces
            $phone = trim($patient['phone']); 
            
            // 2. Find the MATCHING User ID
            // We search the 'users' table. The phone numbers must match EXACTLY.
            $userStmt = $pdo->prepare("SELECT id, full_name FROM users WHERE phone = ? OR unique_id = ? LIMIT 1");
            $userStmt->execute([$phone, $phone]);
            $user = $userStmt->fetch();

            if ($user) {
                // SUCCESS: We found the correct User ID (e.g., 47)
                $target_user_id = $user['id'];
            } else {
                // FAILURE: We stop here.
                // If we use $patient['id'] (e.g., 102), the dashboard (User 47) will never see it.
                die("<div style='color:red; padding:20px; text-align:center;'>
                        <h2>Error: User Not Found</h2>
                        <p>The patient <strong>" . htmlspecialchars($patient['name']) . "</strong> has the phone number: <strong>$phone</strong></p>
                        <p>However, no account in the <strong>Users</strong> table matches this phone number.</p>
                        <p>Please ask the patient to register with this exact phone number, or update their record.</p>
                        <a href='view_patients.php'>Go Back</a>
                     </div>");
            }

            // 3. Get Prescription Details (Optional)
            $medication_name = "Prescribed Medication";
            $dosage = "As directed";
            
            $rxStmt = $pdo->prepare("SELECT * FROM prescriptions WHERE patient_id = ? ORDER BY id DESC LIMIT 1");
            $rxStmt->execute([$patient['id']]);
            $rx = $rxStmt->fetch();

            if ($rx) {
                $medication_name = $rx['medication_name'];
                $dosage = $rx['dosage'];
            }

            // 4. Insert Reminder using the CORRECT User ID
            $insertStmt = $pdo->prepare("INSERT INTO reminders (patient_id, medication_name, dosage, sent_at, status) VALUES (?, ?, ?, NOW(), 'sent')");
            $insertStmt->execute([$target_user_id, $medication_name, $dosage]);

            // 5. Log the action
            $admin_id = $_SESSION['admin_id'] ?? 1;
            $logStmt = $pdo->prepare("INSERT INTO system_logs (user_id, action, details) VALUES (?, ?, ?)");
            $logStmt->execute([$admin_id, "Manual Reminder", "Sent to User ID: $target_user_id"]);

            // 6. Redirect with Success Message
            header("Location: patient_details.php?id=" . $patient['patient_id'] . "&msg=manual_sent");
            exit();

        } else {
            die("Error: Patient not found.");
        }
    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }
} else {
    header("Location: view_patients.php");
    exit();
}
?>