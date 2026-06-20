<?php
session_start();
if (!isset($_SESSION['teacher_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// VULNERABILITY (IDOR): URL parameter 'id' takes precedence without checking if it belongs to logged in session
if (isset($_GET['id'])) {
    $teacher_id = $_GET['id'];
} else {
    $teacher_id = $_SESSION['teacher_id'];
}

// VULNERABILITY (SQL Injection): Raw variable inside SQL query
$sql = "SELECT * FROM teachers WHERE id = $teacher_id";
$result = $conn->query($sql);
$profile = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Faculty Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="navbar">
        <div class="navbar-brand">
            <h1>Faculty Information Architecture</h1>
            <p>BUBT Central Directory</p>
        </div>
        <div>
            <a href="dashboard.php" class="btn-danger" style="background:#6b7280;">Dashboard</a>
        </div>
    </div>

    <div class="container" style="max-width: 650px;">
        <div class="card" style="text-align: center; padding: 40px 20px;">
            <div style="width: 100px; height: 100px; background: #e5e7eb; border-radius: 50%; margin: 0 auto 20px auto; display: flex; align-items: center; justify-content: center; font-size: 40px; border: 3px solid var(--bubt-blue);">👤</div>
            <h2 style="margin-bottom:5px;"><?php echo $profile['name']; ?></h2>
            <p style="color: var(--bubt-green); font-weight: 600; margin: 0 0 30px 0; text-transform: uppercase; font-size: 13px; letter-spacing: 1px;"><?php echo $profile['designation']; ?></p>
            
            <table style="text-align: left; margin: 0;">
                <tr><td style="color: var(--text-muted); font-weight: 600;">Academic Department:</td><td><?php echo $profile['department']; ?></td></tr>
                <tr><td style="color: var(--text-muted); font-weight: 600;">System Access ID:</td><td><code>BUBT-F-00<?php echo $profile['id']; ?></code></td></tr>
                <tr><td style="color: var(--text-muted); font-weight: 600;">Username:</td><td><code><?php echo $profile['username']; ?></code></td></tr>
            </table>
        </div>
    </div>
</body>
</html>
