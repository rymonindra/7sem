<?php
$host = "localhost";
$user = "ripon";
$password = "ripon123!"; 
$database = "cyber_lab";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>
