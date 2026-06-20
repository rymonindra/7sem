<?php
session_start();
include 'db.php';
// ল্যাবের সুবিধার্থে এবং কুকিজ ছাড়া SQLMap টেস্ট করার জন্য সেশন চেক সাময়িকভাবে বন্ধ করা হলো
// if (!isset($_SESSION['teacher_id'])) { header("Location: login.php"); exit(); }

$subject_id = isset($_GET['subject_id']) ? $_GET['subject_id'] : 1; 

// VULNERABILITY (SQL Injection): সরাসরি ইউআরএল প্যারামিটার কুয়েরিতে পাস হচ্ছে
$sub_query = "SELECT * FROM subjects WHERE id = $subject_id";
$sub_res = $conn->query($sub_query);

if ($sub_res && $sub_res->num_rows > 0) {
    $subject = $sub_res->fetch_assoc();
    $dept_id = $subject['dept_id'];
    $subject_name = $subject['subject_name'];
} else {
    // কুয়েরি ফেইল বা মডিফাই হলে ল্যাব এনভায়রনমেন্ট ক্র্যাশ এড়াতে মক ডেটা সেট
    $dept_id = 2;
    $subject_name = "Web Hacking 101";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>BUBT ERP - Take Attendance</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- ড্যাশবোর্ডের মতো হুবহু টপ নেভিগেশন বার -->
    <div class="navbar">
        <div class="navbar-brand">
            <h1>Course Roster: <?php echo $subject_name; ?></h1>
            <p>Verification Sheet | Current Server Date: <?php echo date('Y-m-d'); ?></p>
        </div>
        <div>
            <a href="dashboard.php" class="btn-danger" style="text-decoration:none;">⬅ Back to Dashboard</a>
        </div>
    </div>

    <div class="container" style="max-width:950px;">
        <!-- ড্যাশবোর্ডের মতো কার্ড লেআউট -->
        <div class="card">
            <form method="POST" action="submit_attendance.php">
                <!-- VULNERABILITY (Parameter Tampering) -->
                <input type="hidden" name="subject_id" value="<?php echo $subject_id; ?>">
                
                <table>
                    <tr>
                        <th style="width:80px;">UID</th>
                        <th>Full Name</th>
                        <th style="width:200px;">Status Matrix</th>
                        <th>Evaluation Note</th>
                    </tr>
                    <?php 
                    $student_query = "SELECT * FROM students WHERE dept_id = $dept_id";
                    $students = $conn->query($student_query);
                    if ($students && $students->num_rows > 0) {
                        while($student = $students->fetch_assoc()) { 
                            $sid = $student['id'];
                        ?>
                            <tr>
                                <td><code>#00<?php echo $sid; ?></code></td>
                                <td><strong><?php echo $student['student_name']; ?></strong></td>
                                <td>
                                    <div class="status-pill-group">
                                        <label><input type="radio" name="status[<?php echo $sid; ?>]" value="Present" checked> Present</label>
                                        <label><input type="radio" name="status[<?php echo $sid; ?>]" value="Absent"> Absent</label>
                                    </div>
                                </td>
                                <td>
                                    <!-- ড্যাশবোর্ডের ফিল্টারের মতো ইনপুট বক্স ডিজাইন -->
                                    <input type="text" name="comment[<?php echo $sid; ?>]" class="input-field" placeholder="Add custom evaluation comment..." style="width:100%; margin:0; padding:8px 12px; box-sizing: border-box;">
                                </td>
                            </tr>
                        <?php 
                        }
                    } 
                    ?>
                </table>
                
                <div style="margin-top:20px; text-align:right;">
                    <input type="submit" value="Save & Publish Attendance" class="btn-primary" style="width:auto; padding:12px 35px; font-weight:700;">
                </div>
            </form>
        </div>
    </div>
</body>
</html>
