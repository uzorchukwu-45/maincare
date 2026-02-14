<?php
session_start();

include 'config.php';

// 2. PROCESS REQUEST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_id = $_POST['patient_id'];
    $status = $_POST['status']; // Will be 'Taken' or 'Missed'

    if ($patient_id && $status) {
        try {
            // Insert the log
            $stmt = $pdo->prepare("INSERT INTO adherence_logs (patient_id, status, logged_at) VALUES (?, ?, NOW())");
            $stmt->execute([$patient_id, $status]);

            // Redirect back to refresh the score
            header("Location: patient_details.php?id=$patient_id&msg=dose_logged");
            exit();

        } catch (PDOException $e) {
            die("Error logging dose: " . $e->getMessage());
        }
    } else {
        die("Missing data.");
    }
} else {
    header("Location: view_patients.php");
    exit();
}
?>