<?php
include 'header.php';



// 2. PROCESS FORM
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $patient_input = $_POST['patient_id'] ?? ''; // This might be "102" or "John Doe"
    $medication_name = $_POST['medication_name'] ?? '';
    $dosage = $_POST['dosage'] ?? '';
    $frequency = $_POST['frequency'] ?? '';
    $instructions = $_POST['instructions'] ?? ''; 
    

    // Basic Validation
    if (empty($patient_input) || empty($medication_name)) {
        die("Error: Please fill in all required fields.");
    }

    try {
        // --- SMART SEARCH START ---
        // Check if the input matches an ID, a Name, or a Patient Code (e.g., PLH-102)
        $stmtCheck = $pdo->prepare("SELECT id, name FROM patients WHERE id = ? OR name = ? OR patient_id = ?");
        $stmtCheck->execute([$patient_input, $patient_input, $patient_input]);
        $patient = $stmtCheck->fetch();

        if ($patient) {
            // We found the patient! Let's use their real numeric ID.
            $real_patient_id = $patient['id'];
        } else {
            // Patient doesn't exist in the database
       die("<div style='position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; padding: 30px; border: 1px solid #f5c6cb; background-color: #f8d7da; color: #721c24; border-radius: 10px; font-family: Arial, sans-serif; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'>
        <h2 style='margin-top: 0;'>Error: Patient Not Found</h2>
        <p>We could not find a patient matching <strong>'" . htmlspecialchars($patient_input) . "'</strong>.</p>
        <p>Please check the spelling or go to the 'Add Patient' page first.</p>
        <a href='add_prescription.php' style='display: inline-block; margin-top: 10px; padding: 10px 20px; background-color: #721c24; color: white; text-decoration: none; border-radius: 5px;'>Go Back</a>
     </div>");
        }
        // --- SMART SEARCH END ---


        // 3. INSERT PRESCRIPTION (Using the correct $real_patient_id)
        $sql = "INSERT INTO prescriptions (patient_id, medication_name, dosage, frequency, instructions) 
                VALUES (:patient_id, :medication_name, :dosage, :frequency, :instructions)";
        
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            ':patient_id' => $real_patient_id,
            ':medication_name' => $medication_name,
            ':dosage' => $dosage . ' mg',
            ':frequency' => $frequency,
            ':instructions' => $instructions
        ]);

        // Success!
        header("Location: view_patients.php");
        exit();

    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }

} else {
    header("Location: add_prescription.php");
    exit();
}
?>