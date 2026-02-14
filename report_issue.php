<?php
session_start();

include 'config.php';

// 2. PROCESS FORM
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_id = $_POST['patient_id'];
    $issue_type = $_POST['issue_type'];
    $description = $_POST['description'];

    try {
        $stmt = $pdo->prepare("INSERT INTO issues (patient_id, issue_type, description) VALUES (?, ?, ?)");
        $stmt->execute([$patient_id, $issue_type, $description]);

        // Redirect back with success message
        header("Location: patient_details.php?id=$patient_id&msg=issue_reported");
        exit();

    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>