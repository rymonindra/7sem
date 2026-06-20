<?php
session_start();

$is_sqlmap = isset($_SERVER['HTTP_USER_AGENT']) && stripos($_SERVER['HTTP_USER_AGENT'], 'sqlmap') !== false;

if (!isset($_SESSION['user_id']) && !$is_sqlmap) {
    header("Location: login.php");
    exit();
}

include "db.php";

if (!isset($_GET['id']) && isset($_SESSION['user_id'])) {
    $current_logged_in_id = $_SESSION['user_id'];
    header("Location: profile.php?id=" . $current_logged_in_id);
    exit();
}

$sql_error = "";
$user_data = null;
$posts_result = null;
$total_posts = 0;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // VULNERABLE TO SQLi
    $user_sql = "SELECT id, username FROM users WHERE id = '$id'";
    $user_result = $conn->query($user_sql);
    
    if ($user_result) {
        $user_data = $user_result->fetch_assoc();
        if ($user_data) {
            $u_id = $user_data['id'];
            $posts_sql = "SELECT content, created_at FROM posts WHERE user_id = $u_id ORDER BY created_at DESC";
            $posts_result = $conn->query($posts_sql);
            if ($posts_result) { $total_posts = $posts_result->num_rows; }
        }
    } else {
        $sql_error = $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecLab - Profile Matrix</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .profile-card { text-align: center; padding: 35px 20px; }
        .profile-avatar { width: 90px; height: 90px; background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); color: #f8fafc; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 32px; margin: 0 auto 20px auto; border: 4px solid #1e293b; box-shadow: 0 0 20px rgba(99, 102, 241, 0.3); }
        .profile-name { font-size: 22px; font-weight: 800; color: var(--text-main); margin: 0 0 6px 0; letter-spacing:-0.5px; }
        .profile-stats { font-size: 14px; color: var(--text-muted); font-weight: 500; margin-bottom: 12px; }
        .back-link { margin-top: 15px; display: inline-block; color: #818cf8; text-decoration:none; font-weight:600; }

        <?php if (!empty($sql_error)): ?>
        :root {
            --bg-gradient: linear-gradient(135deg, #0a0506 0%, #17070a 100%);
            --surface: rgba(28, 14, 16, 0.7);
            --brand: #ef5350;
            --brand-glow: rgba(239, 83, 80, 0.4);
            --brand-gradient: linear-gradient(135deg, #ef5350 0%, #b71c1c 100%);
            --border: rgba(239, 83, 80, 0.15);
        }
        .profile-avatar { box-shadow: 0 0 20px var(--brand-glow); border-color: #2d1e20; }
        .post-content { color: #fca5a5; }
        <?php endif; ?>
    </style>
</head>
<body>

<div class="navbar">
    <div class="brand">MySocial <span style="font-size:12px; font-weight:normal; color:var(--text-muted);">(Profile Databank)</span></div>
    <a href="index.php">Home Grid</a>
</div>

<div class="container">
    <div class="sidebar" style="width: 35%;">
        <div class="card profile-card">
            <?php if ($user_data): ?>
                <div class="profile-avatar"><?= strtoupper(substr($user_data['username'], 0, 1)) ?></div>
                <h2 class="profile-name">@<?= $user_data['username'] ?></h2>
                <span class="role-badge">Verified Entity</span>
                <div class="profile-stats">Total Packets Commited: <span style="color: #818cf8; font-weight: 800;"><?= $total_posts ?></span></div>
            <?php else: ?>
                <div class="profile-avatar">?</div>
                <h2 class="profile-name">Null Operator</h2>
            <?php endif; ?>
            <a href="index.php" class="back-link">← Return to Matrix</a>
        </div>
    </div>

    <div class="feed" style="width: 65%;">
        <?php if (!empty($sql_error)): ?>
            <div class="error-dashboard">
                <div class="error-header">⚠️ SQL EXPLOIT SUCCESS: ERROR_BASED_PROFILE_INJECTION</div>
                <div class="error-body"><?= $sql_error ?></div>
                <div class="query-debug"><strong>Target Vector:</strong> <code><?= htmlspecialchars($user_sql) ?></code></div>
            </div>
        <?php endif; ?>

        <h3 style="margin: 0 0 25px 0; font-size: 18px; font-weight: 700; letter-spacing:-0.25px;">Archived Stream Packets</h3>

        <?php if ($posts_result && $posts_result->num_rows > 0): ?>
            <?php while($p_row = $posts_result->fetch_assoc()): ?>
                <div class="card" style="padding: 22px;">
                    <div class="time" style="margin-bottom: 12px; font-size: 13px;">Timestamp: <?= $p_row['created_at'] ?></div>
                    <p class="post-content" style="margin: 0;"><?= $p_row['content'] ?></p>
                </div>
            <?php endwhile; ?>
        <?php elseif (empty($sql_error)): ?>
            <div class="card" style="text-align: center; color: var(--text-muted); padding: 50px;">No recorded activity for this cluster.</div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
