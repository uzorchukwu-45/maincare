<?php 
// 1. MUST start the session at the very top for login to work

include 'header.php'; 

$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 2. Capture data from the form
    // We are using the phone number as the unique_id
    $uniqueId = trim($_POST['unique_id'] ?? ''); 
    $password = $_POST['password'] ?? '';

    if (empty($uniqueId) || empty($password)) {
        $error_msg = "Please enter both your Phone Number and Password.";
    } else {
        try {
            // 3. Look for the user in the database by their unique_id (Phone Number)
            $stmt = $pdo->prepare("SELECT * FROM users WHERE unique_id = ?");
            $stmt->execute([$uniqueId]);
            $user = $stmt->fetch();

            // 4. Verify password and check role
            if ($user && password_verify($password, $user['password'])) {
                
                // 5. Store user info in SESSION variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role']; 

                // 6. Redirect to the correct dashboard based on the 'role' column
                if ($user['role'] == 'Doctor') {
                    header("Location:doctor_dashboard.php");
                } elseif ($user['role'] == 'Pharmacist') {
                    header("Location: pharmacist_dashboard.php");
                } elseif ($user['role'] == 'Nurse') {
                    header("Location: nurse_dashboard.php");
                } else {
                    header("Location: patient_form.php"); // Default for Patients
                }
                exit();

            } else {
                $error_msg = "Invalid Phone Number or Password.";
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
            <h1>Welcome Back</h1>
            <p>Sign in to Parklane Medication Support System</p>
        </div>

        <?php if ($error_msg): ?>
            <p style="color:red; text-align:center; font-weight:bold;"><?php echo $error_msg; ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="input-group">
                <label for="unique_id">Phone Number (ID)</label>
                <input type="text" name="unique_id" placeholder="Enter your registered phone number" required 
                       style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" name="password" placeholder="Enter your password" required 
                       style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc; margin-top: 5px;">
            </div>

            <button type="submit" class="register-btn" 
                    style="width: 100%; padding: 12px; background-color: #4e5d78; color: white; border: none; border-radius: 5px; margin-top: 20px; cursor: pointer;">
                Sign In
            </button>
            
            <div class="form-footer" style="text-align: center; margin-top: 15px;">
                <p>Don't have an account? <a href="register.php">Create Account</a></p>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>