<?php
session_start();
require_once 'config.php';

// 1. Security Check (Ensure it's a logged-in Patient)
if (!isset($_SESSION['patient_logged_in'])) {
    // Adjust this session key based on your login.php
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = $_SESSION['staff_id']; // Assuming you store Patient ID here
    $prescription_id = $_POST['prescription_id'];
    $drug_name = $_POST['drug_name'];
    $status = $_POST['status']; // Will be 'Taken' or 'Missed'

    try {
        // 2. Check if already recorded for TODAY to prevent double-clicking
        $check = $pdo->prepare("
            SELECT id FROM adherence_records 
            WHERE patient_id = ? AND prescription_id = ? 
            AND DATE(recorded_at) = CURDATE()
        ");
        $check->execute([$patient_id, $prescription_id]);

        if ($check->rowCount() == 0) {
            // 3. Save the new record
            $stmt = $pdo->prepare("INSERT INTO adherence_records (patient_id, prescription_id, drug_name, status) VALUES (?, ?, ?, ?)");
            $stmt->execute([$patient_id, $prescription_id, $drug_name, $status]);
            
            $_SESSION['success_msg'] = "Medication marked as $status.";
        } else {
            $_SESSION['error_msg'] = "You have already marked this medication for today.";
        }

    } catch (PDOException $e) {
        $_SESSION['error_msg'] = "Error: " . $e->getMessage();
    }
}

// Redirect back to the tracking page
header("Location: track_adherence.php");
exit();
?>