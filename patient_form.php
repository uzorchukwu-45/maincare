<?php
include 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

if (isset($_POST['skip'])) {
        header("Location: dashboard.php");
        exit();
    }
    // 1. Capture data from the form
    $patient_id     = $_POST['patient_id'];
    $name           = $_POST['name'];
    $age            = $_POST['age'];
    $phone          = $_POST['phone'];
    $condition_name = $_POST['condition_name'];
    $status         = 'Active'; // Default status as seen in your DB image

    try {
        // 2. Prepare the SQL Statement
        $sql = "INSERT INTO patients (patient_id, name, age, phone, condition_name, status) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        
        // 3. Execute and Redirect
        if ($stmt->execute([$patient_id, $name, $age, $phone, $condition_name, $status])) {
            // Set session variables for the dashboard
            $_SESSION['user_id'] = $patient_id;
            $_SESSION['full_name'] = $name;
            
            header("Location: dashboard.php");
            exit();
        }
    } catch (PDOException $e) {
        $error = "Registration failed: " . $e->getMessage();
    }
}
?>





<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<style>
        body { background-color: #f8f9fa; }
        .reg-card { max-width: 500px; margin: 50px auto; border: none; border-radius: 15px; }
        .btn-submit { background-color: #2c3e50; color: white; }
    </style>
    
  </head>
  <body>
   

<div class="container">
    <div class="card reg-card shadow-sm">
        <div class="card-header bg-white py-3">
            <h4 class="text-center mb-0">Patient Profile Setup</h4>
        </div>
        <div class="card-body p-4">
            <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Patient ID (e.g., PLH-102)</label>
                    <input type="text" name="patient_id" class="form-control" placeholder="Enter ID" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter full name" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Age</label>
                        <input type="number" name="age" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" placeholder="080..." required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Medical Condition</label>
                    <input type="text" name="condition_name" class="form-control" placeholder="e.g. Hypertension" required>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-submit btn-lg">Complete Registration</button>

                </div>
                <br>
                
           <div class="d-grid">
                   <button type="submit" class="btn btn-outline-secondary btn-lg" name="skip" formnovalidate>Skip for Now</button>
          </div>
            </form>
        </div>
    </div>
</div>



























    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
  </body>
</html>
<?php   include 'footer.php';   ?>