<?php
session_start();
require_once 'config.php'; // Ensure database connection is available

// 1. Security Check: Ensure a user is logged in
if (!isset($_SESSION['admin_logged_in']) && !isset($_SESSION['staff_logged_in'])) {
    // You might need to adjust this depending on who is allowed to add vitals (Admin or Nurse)
    // For now, if no session, redirect to login
    if (!isset($_SESSION['full_name'])) {
        header("Location: login.php");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 2. Collect Data from the Form
    $patient_input = trim($_POST['patient_info']);
    $bp = $_POST['bp'];
    $temp = $_POST['temp'];
    $heart_rate = $_POST['heart_rate'];
    $weight = $_POST['weight'];
    $notes = $_POST['notes'];
    
    // Capture who is recording this (The Nurse/Doctor logged in)
    $recorded_by = $_SESSION['full_name'] ?? 'Unknown Staff';

    try {
        // 3. Verify the Patient Exists First
        // We assume the user entered the 'Staff ID' / 'Patient ID' in the search box
        $check_stmt = $pdo->prepare("SELECT patient_id FROM patients WHERE patient_id = ?");
        $check_stmt->execute([$patient_input]);
        
        if ($check_stmt->rowCount() > 0) {
            
            // 4. Insert the Vitals into the Database
            $sql = "INSERT INTO vitals (patient_id, bp, temperature, heart_rate, weight, notes, recorded_by) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$patient_input, $bp, $temp, $heart_rate, $weight, $notes, $recorded_by]);

            // 5. Success: Alert and Redirect
            echo "<script>
                alert('Vital signs updated successfully for Patient: $patient_input');
                window.location.href = 'nurse_dashboard.php'; // Redirect to nurse dashboard
            </script>";
            
        } else {
            // Error: Patient ID not found
            echo "<script>
                alert('Error: Patient ID \"$patient_input\" not found in the system. Please check the ID and try again.');
                window.history.back();
            </script>";
        }

    } catch (PDOException $e) {
        echo "Database Error: " . $e->getMessage();
    }
} else {
    // If someone tries to open this file directly without submitting the form
    header("Location: vitals.php");
    exit();
}
?>