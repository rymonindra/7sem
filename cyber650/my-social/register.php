<?php
ini_set('session.cookie_httponly', 0);
ini_set('session.cookie_secure', 0);
ini_set('session.cookie_samesite', 'None');
session_start();
include "db.php";
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password']; 

    if (!empty($username) && !empty($password)) {
        $check_sql = "SELECT id FROM users WHERE username = '$username'";
        $result = $conn->query($check_sql);

        if ($result && $result->num_rows > 0) {
            $error = "Username already exists!";
        } else {
            // VULNERABLE SQL & PLAIN TEXT PASSWORD INSERT
            $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
            if ($conn->query($sql)) {
                $success = "Identity created! <a href='login.php' style='color:#6366f1; font-weight:700;'>Login here</a>";
            } else {
                $error = "SQL Error: " . $conn->error;
            }
        }
    } else {
        $error = "All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SecLab - Identity Provisioning</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
        }
        .auth-box { 
            width: 360px; 
        }
        .auth-box h2 { 
            margin: 0 0 24px 0; 
            font-size: 24px; 
            font-weight: 800; 
            color: var(--text-main); 
        }
        .input-group {
            margin-bottom: 18px;
        }
        .auth-box button { 
            width: 100%; 
            padding: 14px; 
            font-size: 15px; 
            font-weight: 700;
            border-radius: 12px;
            margin-top: 8px; 
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        .auth-box button:hover {
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.4);
        }
        .success-box {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
            padding: 14px;
            border-radius: 14px;
            font-size: 14px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .auth-footer { 
            margin-top: 24px; 
            text-align: center; 
            font-size: 14px; 
            color: var(--text-muted); 
        }
        .auth-footer a { 
            color: #818cf8; 
            text-decoration: none; 
            font-weight: 700; 
        }
    </style>
</head>
<body>

<div class="card auth-box">
    <h2>Register Identity</h2>
    
    <?php if(!empty($error)): ?>
        <div class="error-dashboard" style="padding: 14px; margin-bottom: 16px;">
            <div class="error-header" style="font-size:13px;">⚠️ PROVISION_EXCEPTION</div>
            <div class="error-body" style="font-size:12px;"><?= $error ?></div>
        </div>
    <?php endif; ?>

    <?php if(!empty($success)): ?>
        <div class="success-box"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
        <div class="input-group">
            <input type="text" name="username" placeholder="Choose node username" required>
        </div>
        <div class="input-group">
            <input type="password" name="password" placeholder="Create cluster passphrase" required>
        </div>
        <button type="submit">Provision Account †</button>
    </form>

    <div class="auth-footer">
        Already registered? <a href="login.php">Log In Here</a>
    </div>
</div>

</body>
</html>
