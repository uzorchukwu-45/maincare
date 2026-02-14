<?php
session_start();
require_once 'config.php';

// Check if the user is logged in (Security Requirement 4.2)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_id = $_SESSION['user_id'];
    $prescription_id = $_POST['p_id'];
    $status = $_POST['status']; // Will be 'YES' or 'NO' (FR10)

    try {
        // Prepare the SQL to log the adherence (FR12)
        $sql = "INSERT INTO adherence_logs (patient_id, prescription_id, status) 
                VALUES (:p_id, :pre_id, :stat)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':p_id'   => $patient_id,
            ':pre_id' => $prescription_id,
            ':stat'   => $status
        ]);

        // Success! Redirect back to dashboard to see updated adherence percentage
        header("Location: dashboard.php?msg=success");
        exit();

    } catch (PDOException $e) {
        // Handle database errors gracefully (Reliability Requirement 4.4)
        die("Error logging adherence: " . $e->getMessage());
    }
} else {
    // If someone tries to access this file directly without posting data
    header("Location: dashboard.php");
    exit();
}
?>

