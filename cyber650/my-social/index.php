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
    header("Location: index.php?id=" . $current_logged_in_id);
    exit();
}
$search_query = "";
if (isset($_GET['search'])) { $search_query = $_GET['search']; }

$sql = "SELECT posts.id, posts.content, posts.created_at, posts.user_id as post_owner_id, users.username FROM posts JOIN users ON posts.user_id = users.id";
$sql_error = "";
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    if (!empty($search_query)) {
        $sql .= " WHERE (users.id = '$id' OR '$id' = '$id') AND posts.content LIKE '%$search_query%'";
    } else {
        $sql .= " WHERE users.id = '$id' OR '$id' = '$id'";
    }
}
$sql .= " ORDER BY posts.created_at DESC";
$result = $conn->query($sql);
if (!$result) { $sql_error = $conn->error; }

$user_display_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'LabUser';
$current_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
$avatar_letter = strtoupper(substr($user_display_name, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SecLab - Premium Cyber Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <?php if (!empty($sql_error)): ?>
    <style>
        :root { --bg-gradient: linear-gradient(135deg, #0a0506 0%, #17070a 100%); --surface: rgba(28, 14, 16, 0.7); --brand: #ef5350; --brand-glow: rgba(239, 83, 80, 0.4); --brand-gradient: linear-gradient(135deg, #ef5350 0%, #b71c1c 100%); --border: rgba(239, 83, 80, 0.15); }
        textarea, .header-search input, .comment-form input { background: rgba(12, 5, 6, 0.6); color: #fecdd3; }
        .comment-list { background: rgba(20, 8, 10, 0.4); border-color: rgba(239, 83, 80, 0.15); }
        .avatar, .sidebar-avatar { box-shadow: 0 0 20px var(--brand-glow); }
        .username-tag { background: rgba(12, 5, 6, 0.7); border-color: rgba(239, 83, 80, 0.2); color: #fecdd3; }
        .role-badge { background: rgba(239, 83, 80, 0.15); color: #f87171; border-color: rgba(239, 83, 80, 0.3); }
        .post-content { color: #fca5a5; } .comment-user { color: #f87171; }
        .like-btn:hover { background: rgba(239, 83, 80, 0.2); border-color: var(--brand); box-shadow: 0 0 10px var(--brand-glow); }
    </style>
    <?php endif; ?>
</head>
<body>
<div class="navbar">
    <div class="navbar-left">
        <div class="brand">MySocial <span style="font-size:11px; font-weight:normal; color:var(--text-muted);">(Lab Cluster)</span></div>
        <form method="GET" class="header-search">
            <input type="hidden" name="id" value="<?= htmlspecialchars($_GET['id'] ?? '') ?>">
            <input type="text" name="search" placeholder="Execute content query..." value="<?= htmlspecialchars($search_query) ?>">
            <button type="submit">Search</button>
        </form>
    </div>
    <div class="navbar-right">
        <a href="profile.php?id=<?= $current_user_id ?>">My Profile</a>
        <a href="logout.php" style="background: rgba(239, 68, 68, 0.15); color: #f87171; border-color: rgba(239, 68, 68, 0.3);">Terminate Session</a>
    </div>
</div>
<div class="container">
    <div class="sidebar">
        <div class="profile-cover"></div>
        <div class="profile-widget">
            <div class="profile-avatar-container"><div class="sidebar-avatar"><?= $avatar_letter ?></div></div>
            <h3>Node Operator</h3><span class="role-badge">Root Investigator</span><br>
            <div class="username-tag"><span style="color: #818cf8; font-weight:bold;">@</span><?= htmlspecialchars($user_display_name) ?></div>
        </div>
    </div>
    <div class="feed">
        <?php if (!empty($sql_error)): ?>
            <div class="error-dashboard">
                <div class="error-header">⚠️ EXPLOIT SUCCESS: ERROR_BASED_SQL_INJECTION</div>
                <div class="error-body"><?= $sql_error ?></div>
                <div class="query-debug"><strong>Target Query:</strong> <code><?= htmlspecialchars($sql) ?></code></div>
            </div>
        <?php endif; ?>
        <?php if (!empty($search_query)): ?>
            <div class="card" style="padding: 18px 24px; font-size: 14px; color: var(--text-muted);">
                Token reflection buffer: <strong><?= $search_query ?></strong>
                <a href="index.php" style="float: right; text-decoration: none; color: var(--brand);">Clear Buffer</a>
            </div>
        <?php endif; ?>
        <div class="card">
            <form action="post.php" method="POST">
                <textarea name="content" placeholder="Publish payload or status to global matrix..." required></textarea>
                <div style="text-align: right;"><button type="submit">Commit Packet</button></div>
            </form>
        </div>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <div class="post-header">
                        <div class="avatar"><?= strtoupper(substr($row['username'], 0, 1)) ?></div>
                        <div class="user-info"><span class="user"><?= $row['username'] ?></span><span class="time"><?= $row['created_at'] ?></span></div>
                    </div>
                    <p class="post-content"><?= $row['content'] ?></p>
                    <div class="post-actions" style="display: flex; gap: 12px;">
                        <form action="post.php" method="POST" style="margin: 0;">
                            <input type="hidden" name="post_id" value="<?= $row['id'] ?>"><input type="hidden" name="action" value="like">
                            <button type="submit" class="like-btn <?= ($conn->query("SELECT id FROM likes WHERE post_id = ".$row['id']." AND user_id = $current_user_id")->num_rows > 0) ? 'liked' : '' ?>">
                                💙 Like (<?= $conn->query("SELECT COUNT(*) as total FROM likes WHERE post_id = ".$row['id'])->fetch_assoc()['total'] ?>)
                            </button>
                        </form>
                        <?php if (intval($row['post_owner_id']) === intval($current_user_id)): ?>
                            <form action="post.php" method="POST" style="margin: 0;">
                                <input type="hidden" name="post_id" value="<?= $row['id'] ?>"><input type="hidden" name="action" value="delete">
                                <button type="submit" class="like-btn" style="color: #f87171; border-color: rgba(239, 68, 68, 0.2); background: rgba(239, 68, 68, 0.05);">🗑️ Delete Post</button>
                            </form>
                        <?php endif; ?>
                    </div>
                    <div class="comment-section">
                        <?php
                        $post_id = $row['id'];
                        $comment_sql = "SELECT comments.comment_text, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = $post_id ORDER BY comments.created_at ASC";
                        $comment_result = $conn->query($comment_sql);
                        if ($comment_result && $comment_result->num_rows > 0) {
                            while ($c_row = $comment_result->fetch_assoc()) {
                                echo "<div class='comment-list'><span class='comment-user'>@" . $c_row['username'] . "</span>: " . $c_row['comment_text'] . "</div>";
                            }
                        }
                        ?>
                        <form action="post.php" method="POST" class="comment-form">
                            <input type="hidden" name="post_id" value="<?= $row['id'] ?>"><input type="text" name="comment_text" placeholder="Append comment packet..." required><button type="submit" name="submit_comment">Comment</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php elseif (empty($sql_error)): ?>
            <div class="card" style="text-align: center; color: var(--text-muted); padding: 50px;">No datagrams parsed in timeline.</div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
