<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$is_sqlmap = isset($_SERVER['HTTP_USER_AGENT']) && stripos($_SERVER['HTTP_USER_AGENT'], 'sqlmap') !== false;

if (!isset($_SESSION['user_id']) && !$is_sqlmap) {
    header("Location: login.php");
    exit();
}

include "db.php";

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 1;

        // --- কন্ডিশন ১: পোস্ট ডিলিট হ্যান্ডলার (VULNERABLE TO SQLi & CSRF) ---
        if (isset($_POST['action']) && $_POST['action'] === 'delete') {
            $post_id = $_POST['post_id'];
            $sql = "DELETE FROM posts WHERE id = '$post_id'";
            
            if ($conn->query($sql)) {
                header("Location: index.php");
                exit();
            } else {
                throw new Exception("Database Query Error: " . $conn->error);
            }
        }

        // --- কন্ডিশন ২: লাইক হ্যান্ডলার (VULNERABLE TO SQLi) ---
        elseif (isset($_POST['action']) && $_POST['action'] === 'like') {
            $post_id = $_POST['post_id'];
            $check_sql = "SELECT id FROM likes WHERE post_id = '$post_id' AND user_id = $user_id";
            $check_result = $conn->query($check_sql);

            if ($check_result && $check_result->num_rows > 0) {
                $sql = "DELETE FROM likes WHERE post_id = '$post_id' AND user_id = $user_id";
            } else {
                $sql = "INSERT INTO likes (post_id, user_id) VALUES ('$post_id', $user_id)";
            }

            if ($conn->query($sql)) {
                header("Location: index.php");
                exit();
            } else {
                throw new Exception("Database Query Error: " . $conn->error);
            }
        }

        // --- কন্ডিশন ৩: কমেন্ট সাবমিট হ্যান্ডলার (VULNERABLE TO STORED XSS) ---
        elseif (isset($_POST['submit_comment'])) {
            $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
            $comment_text = isset($_POST['comment_text']) ? $_POST['comment_text'] : '';

            $sql = "INSERT INTO comments (post_id, user_id, comment_text) VALUES ($post_id, $user_id, '$comment_text')";
            if ($conn->query($sql)) {
                header("Location: index.php");
                exit();
            } else {
                throw new Exception("Database Query Error: " . $conn->error);
            }
        }

        // --- কন্ডিশন ৪: XML External Entity (XXE) হ্যান্ডলার ---
        elseif (isset($_POST['import_xml'])) {
            $xml_data = isset($_POST['xml_data']) ? $_POST['xml_data'] : '';
            if (empty($xml_data)) { throw new Exception("XML data is empty!"); }

            $dom = new DOMDocument();
            $dom->loadXML($xml_data, LIBXML_NOENT | LIBXML_DTDLOAD);
            $xml = simplexml_import_dom($dom);
            $bio = isset($xml->bio) ? (string)$xml->bio : "No bio found in XML";
            
            echo "
            <div style='font-family: Arial; background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%); height: 100vh; display: flex; justify-content: center; align-items: center; margin:0;'>
                <div style='background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(16px); padding: 30px; border-radius: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); width: 400px; border:1px solid rgba(255,255,255,0.08); color:white;'>
                    <h3 style='background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-top:0;'>XML Parsed Successfully!</h3>
                    <p style='font-weight:bold; color:#94a3b8;'>Extracted DTD Bio Stream:</p>
                    <pre style='background: rgba(15, 23, 42, 0.5); color:#e2e8f0; padding: 12px; border-radius: 12px; border:1px solid rgba(255,255,255,0.05); overflow-x: auto; white-space: pre-wrap; word-wrap: break-word; font-family: monospace;'>".htmlspecialchars($bio)."</pre>
                    <br>
                    <a href='index.php' style='display: inline-block; background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); color: white; text-decoration: none; padding: 10px 20px; border-radius: 10px; font-weight: bold;'>Return to Node Matrix</a>
                </div>
            </div>";
            exit();
        }

        // --- কন্ডিশন ৫: সাধারণ টেক্সট পোস্ট হ্যান্ডলার ---
        else {
            $content = isset($_POST['content']) ? $_POST['content'] : '';
            $sql = "INSERT INTO posts (user_id, content) VALUES ($user_id, '$content')";
            if ($conn->query($sql)) {
                header("Location: index.php");
                exit();
            } else {
                throw new Exception("Database Query Error: " . $conn->error);
            }
        }
    } else {
        header("Location: index.php");
        exit();
    }
} catch (Throwable $e) {
    echo "<div style='font-family: monospace; background: rgba(26, 15, 17, 0.9); color: #ffcdd2; padding: 25px; margin: 30px; border-radius: 16px; border: 1px solid #4a1518; box-shadow: 0 0 30px rgba(239, 83, 80, 0.2);'><h3 style='color:#ef5350; margin-top:0;'>⚠️ [Lab Debugger] PHP Critical Exception Caught:</h3><strong>Error Message:</strong> " . htmlspecialchars($e->getMessage()) . "<br><strong>Trace File:</strong> " . htmlspecialchars($e->getFile()) . " on line " . $e->getLine() . "<br><br><a href='index.php' style='color:#ef5350; font-weight:bold; text-decoration:none;'>[Back to Main Node]</a></div>";
    exit();
}
?>
