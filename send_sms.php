<?php
session_start();
include 'config.php';


// 2. CHECK IF FORM WAS SUBMITTED
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['patient_id'])) {
    
    $patient_id = (int)$_POST['patient_id'];

    try {
        // 3. FETCH PATIENT PHONE NUMBER
        $stmt = $pdo->prepare("SELECT name, phone FROM patients WHERE id = ?");
        $stmt->execute([$patient_id]);
        $patient = $stmt->fetch();

        if ($patient) {
            $phone = $patient['phone'];
            $name = $patient['name'];

            // --- SMS SENDING LOGIC (SIMULATION) ---
            // In a real app, you would use an API like Twilio or Termii here.
            // Example:
            // $message = "Hello $name, this is a reminder to take your medication.";
            // send_sms($phone, $message); 
            
            // For now, we simulate success:
            // Log the action (Optional: You could insert into a 'message_logs' table here)
            
            // 4. REDIRECT BACK WITH SUCCESS MESSAGE
            header("Location: patient_details.php?id=$patient_id&msg=sms_sent");
            exit();

        } else {
            die("Patient not found.");
        }

    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }

} else {
    // If accessed directly without clicking the button
    header("Location: view_patients.php");
    exit();
}
?>