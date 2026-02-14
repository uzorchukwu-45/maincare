<?php
// 1. Database Credentials
$host = 'localhost';
$dbname = 'dosecare'; 
$username = 'root';
$password = '';

// 2. Connect to Database (Only if not already connected)
if (!isset($pdo)) {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Database Connection Failed: " . $e->getMessage());
    }
}

// 3. FIX: Check if function exists before creating it
// This prevents the "Cannot redeclare" Fatal Error
if (!function_exists('logSystemActivity')) {
    
    function logSystemActivity($pdo, $user_id, $action, $details) {
        try {
            // Ensure $details is not null if your DB doesn't allow nulls
            $details = $details ?? ''; 
            
            $stmt = $pdo->prepare("INSERT INTO system_logs (user_id, action, details, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$user_id, $action, $details]);
        } catch (PDOException $e) {
            // If logging fails, we don't want to crash the whole site, so we just continue
        }
    }
    
}
?>