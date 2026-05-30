<?php
$host = 'localhost';
$port = '3307';
$dbname = 'cybercrime_db';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Database connected successfully!";
}
?>
