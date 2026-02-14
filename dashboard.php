<?php include 'header.php'; ?>

<?php
// Check Authentication (FR3)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];

// --- Fetch the Latest Reminder for this Patient ---
$reminder_stmt = $pdo->prepare("SELECT * FROM reminders WHERE patient_id = ? ORDER BY sent_at DESC LIMIT 1");
$reminder_stmt->execute([$user_id]);
$latest_reminder = $reminder_stmt->fetch();
// ------------------------------------------------------------

// Fetch Adherence Data
$stmt = $pdo->prepare("SELECT status FROM adherence_logs WHERE patient_id = ?");
$stmt->execute([$user_id]);
$logs = $stmt->fetchAll(); 
$total_doses = count($logs);

// Updated to match your DB where status might be 1, Taken, or YES
$taken_doses = count(array_filter($logs, function($l) {
    return in_array($l['status'], ['Taken', 'YES', '1', 1]);
})); 

$adherence_rate = ($total_doses > 0) ? round(($taken_doses / $total_doses) * 100) : 0;

// --- FIXED: Fetch ALL Prescriptions for this Patient ---
$p_stmt = $pdo->prepare("SELECT * FROM prescriptions WHERE patient_id = ?");
$p_stmt->execute([$user_id]);
$prescriptions = $p_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DoseCare Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    
    <style>
        /* Entrance Animation (Slides Down) */
        @keyframes slideDown {
            from { 
                transform: translateY(-100%); 
                opacity: 0; 
                margin-top: -50px;
            }
            to { 
                transform: translateY(0); 
                opacity: 1; 
                margin-top: 0;
            }
        }

        .reminder-alert {
            background-color: #e8eaf6; 
            border-left: 5px solid #1a237e; 
            padding: 20px; 
            margin-bottom: 25px; 
            border-radius: 8px; 
            display: flex; 
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            animation: slideDown 0.8s ease-out forwards;
            transition: all 0.5s ease-in;
        }

        .slide-up-exit {
            transform: translateY(-100%);
            opacity: 0;
            margin-bottom: -100px;
            pointer-events: none;
        }

        .btn-mark-taken {
            background-color: #00bcd4;
            color: white;
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-mark-taken:hover { background-color: #1a237e; }
    </style>
</head>
<body>
    <div class="dashboard-container">
    <div class="sidebar">
        <h2>DoseCare</h2>
        <p>User: <?php echo htmlspecialchars($user_name); ?></p>
    </div>

    <div class="main-content">
        <header>
            <h1>Outpatient Adherence Portal</h1>
        </header>

        <?php if ($latest_reminder): ?>
        <section class="reminder-section">
            <div class="reminder-alert" id="reminderBox">
                <div class="reminder-content">
                    <h4>🔔 New Medication Reminder</h4>
                    <p>
                        Time to take: <strong><?php echo htmlspecialchars($latest_reminder['medication_name']); ?></strong> 
                        (<?php echo htmlspecialchars($latest_reminder['dosage']); ?>)
                    </p>
                    <small style="color: #666;">Sent: <?php echo date('h:i A, M d', strtotime($latest_reminder['sent_at'])); ?></small>
                </div>
                
                <form method="POST" action="log_adherence.php" id="reminderForm">
                    <input type="hidden" name="reminder_id" value="<?php echo $latest_reminder['id']; ?>">
                    <input type="hidden" name="medication_name" value="<?php echo htmlspecialchars($latest_reminder['medication_name']); ?>">
                    <input type="hidden" name="status" value="Taken">
            
                </form>
            </div>
        </section>
        <?php endif; ?>
        
        <section class="stat-container">
            <div class="card">
                <h3>Overall Adherence Rate</h3>
                <div class="progress-ring">
                    <span class="percentage"><?php echo $adherence_rate; ?>%</span>
                </div>
                <?php if ($adherence_rate < 60): ?>
                    <p style="color:red; font-weight:bold; margin-top:10px;">⚠️ Adherence Critical: Below 60%</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="table-section">
            <h3>Active Prescriptions</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f8f9fa; text-align: left;">
                        <th style="padding: 12px;">Medication</th>
                        <th style="padding: 12px;">Dosage</th>
                        <th style="padding: 12px;">Frequency</th>
                        <th style="padding: 12px;">Duration</th>
                        <th style="padding: 12px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($prescriptions) > 0): ?>
                        <?php foreach ($prescriptions as $med): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px;"><?php echo htmlspecialchars($med['medication_name']); ?></td>
                            <td style="padding: 12px;"><?php echo htmlspecialchars($med['dosage']); ?></td>
                            <td style="padding: 12px;"><?php echo htmlspecialchars($med['frequency']); ?></td>
                            <td style="padding: 12px;"><?php echo htmlspecialchars($med['duration']); ?></td>
                            <td style="padding: 12px;">
                                <form method="POST" action="log_adherence.php" style="display:inline-flex; gap:5px;">
                                    <input type="hidden" name="medication_name" value="<?php echo htmlspecialchars($med['medication_name']); ?>">
                                    <button type="submit" name="status" value="Taken" style="background:#28a745; color:white; border:none; padding:5px 10px; border-radius:4px; cursor:pointer;">Taken</button>
                                    <button type="submit" name="status" value="Missed" style="background:#dc3545; color:white; border:none; padding:5px 10px; border-radius:4px; cursor:pointer;">Missed</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="padding: 20px; text-align: center; color: #888;">No active prescriptions found in your record.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </div>
</div>

<script>
    function dismissAndSubmit() {
        const box = document.getElementById('reminderBox');
        const form = document.getElementById('reminderForm');
        if(box) box.classList.add('slide-up-exit');
        setTimeout(() => { form.submit(); }, 500); 
    }
</script>

</body>
</html>
<?php include 'footer.php'; ?>