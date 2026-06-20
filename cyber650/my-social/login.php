<?php
ini_set('session.cookie_httponly', 0);
ini_set('session.cookie_secure', 0);
ini_set('session.cookie_samesite', 'None');
session_start();
include "db.php";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        // VULNERABLE TO SQL INJECTION
        $sql = "SELECT id, username FROM users WHERE username = '$username' AND password = '$password'";
        $result = $conn->query($sql);

        if ($result && $row = $result->fetch_assoc()) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid Login. SQL Error: " . $conn->error;
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
    <title>SecLab - Cyber Authentication Gateway</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { 
            display: flex; 
            flex-direction: column; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
        }
        .welcome-text { 
            font-size: 28px; 
            font-weight: 800; 
            background: var(--brand-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 28px; 
            letter-spacing: -0.75px; 
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
        .auth-footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="welcome-text">MySocial Secure Matrix</div>

<div class="card auth-box">
    <h2>Login Portal</h2>
    
    <?php if(!empty($error)): ?>
        <div class="error-dashboard" style="padding: 14px; margin-bottom: 16px;">
            <div class="error-header" style="font-size:13px;">⚠️ SQL_EXCEPTION_CAUGHT</div>
            <div class="error-body" style="font-size:12px;"><?= $error ?></div>
        </div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
        <div class="input-group">
            <input type="text" name="username" placeholder="Enter investigator token" required>
        </div>
        <div class="input-group">
            <input type="password" name="password" placeholder="Enter access passphrase" required>
        </div>
        <button type="submit">Access Workspace →</button>
    </form>

    <div class="auth-footer">
        New investigator Node? <a href="register.php">Create Identity</a>
    </div>
</div>

</body>
</html>
