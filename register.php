<?php 
include 'header.php'; 


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Collect and sanitize input data
    $fullname = trim($_POST['fullname'] ?? '');
    $age      = intval($_POST['age'] ?? 0);
    $phone    = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? 'Patient'; 
    
    // FIX: Generate or capture a unique_id. 
    // If you want the phone number to be the ID, do this:
    $uniqueId = $phone; 

    // 2. Updated Validation - ensure $uniqueId is NOT empty
    if (empty($fullname) || empty($uniqueId) || empty($password)) {
        $error_msg = "Please fill in all required fields.";
    } else {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // 3. Check for actual duplicate ID instead of role
            $checkStmt = $pdo->prepare("SELECT id FROM users WHERE unique_id = ?");
            $checkStmt->execute([$uniqueId]);
            
            if ($checkStmt->rowCount() > 0) {
                echo "<script>alert('Error: This ID is already registered.'); window.history.back();</script>";
                exit();
            } else {
                // 4. Corrected Insert Statement including unique_id
                $sql = "INSERT INTO users (full_name, age, phone, password, role, unique_id) 
                        VALUES (:full_name, :age, :phone, :password, :role, :unique_id)";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':full_name' => $fullname,
                    ':age'       => $age,
                    ':phone'     => $phone,
                    ':password'  => $hashedPassword,
                    ':role'      => $role,
                    ':unique_id' => $uniqueId
                ]);

                echo "<script>alert('Registration Successful!'); window.location.href='login.php';</script>";
                exit();
            }
        } catch (PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }
    }
}
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Create Account</h1>
            <p>Join the Parklane Medication Support System</p>
        </div>

        <form id="registerForm" action="register.php" method="POST">
            <div class="input-group">
                <label for="fullname">Full Name</label>
                <input type="text" id="fullname" name="fullname" placeholder="Enter your full name" required>
            </div>

            <div class="form-row" style="display: flex; gap: 10px;">
                <div class="input-group" style="flex: 1;">
                    <label for="age">Age</label>
                    <input type="number" id="age" name="age" min="1" max="120" required>
                </div>
                
                <div class="input-group" style="flex: 2;">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="08012345678" required>
                </div>
            </div>

            <div class="input-group">
                <label for="role">System Role</label>
                <select name="role" id="role" class="form-select" style="width: 100%; padding: 10px; border-radius: 5px;" required>
                    <option value="" disabled selected>Select your role...</option>
                    <option value="Doctor">Doctor</option>
                    <option value="Pharmacist">Pharmacist</option>
                    <option value="Nurse">Nurse</option>
                    <option value="Patient">Patient</option>
                </select>
            </div>

            <div class="input-group">
                <label for="password">Create Password</label>
                <input type="password" id="password" name="password" placeholder="At least 8 characters" required>
            </div>

            <button type="submit" class="register-btn" style="width: 100%; padding: 10px; background: #2a1735ce; color: white; border: none; border-radius: 5px; cursor: pointer;">Complete Registration</button>
            
            <div class="form-footer" style="text-align: center; margin-top: 15px;">
                <p>Already have an account? <a href="login.php">Sign In</a></p>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>