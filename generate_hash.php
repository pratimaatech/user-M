<?php
/**
 * STEP 1: Is file ko browser mein kholo
 *         http://localhost/user-management/generate_hash.php
 *
 * STEP 2: Wahan jo SQL query milegi, use phpMyAdmin mein run karo
 *
 * STEP 3: Is file ko DELETE kar do
 */

$password = 'admin123';
$hash     = password_hash($password, PASSWORD_BCRYPT);

// Verify karo ke hash sahi kaam kar raha hai
$ok = password_verify($password, $hash) ? 'YES - Working!' : 'NO - Error!';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Hash Generator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">
<div class="card" style="max-width:700px">
    <div class="card-header bg-primary text-white">
        <strong>Password Hash Generator</strong>
    </div>
    <div class="card-body">
        <p><strong>Password:</strong> <code><?= $password ?></code></p>
        <p><strong>Hash:</strong> <code><?= $hash ?></code></p>
        <p><strong>Verify check:</strong> <span class="text-success fw-bold"><?= $ok ?></span></p>

        <hr>
        <p class="fw-bold text-danger">Step 1: Pehle phpMyAdmin mein database open karo</p>
        <p class="fw-bold text-danger">Step 2: Yeh SQL query run karo:</p>

        <div class="bg-dark text-white p-3 rounded small">
            USE user_management;<br><br>
            -- Agar admin table mein row already hai:<br>
            UPDATE admin SET password = '<?= $hash ?>' WHERE username = 'admin';<br><br>
            -- Agar row nahi hai:<br>
            INSERT INTO admin (username, password) VALUES ('admin', '<?= $hash ?>');
        </div>

        <div class="alert alert-warning mt-3 mb-0">
            <strong>Step 3:</strong> Is file ko delete karo: <code>generate_hash.php</code>
        </div>
    </div>
</div>
</body>
</html>
