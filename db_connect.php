<?php
$host = "localhost";  // Database host
$user = "root";       // Your MySQL username
$pass = "";           // Your MySQL password
$dbname = "testing";  // Your database name

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
