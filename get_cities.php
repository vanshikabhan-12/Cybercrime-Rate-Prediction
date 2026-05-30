<?php
include 'db_connection.php';

$state = $_GET['state'];

// Fetch cities for the selected state
$sql = "SELECT DISTINCT city FROM cybercrime_db WHERE state='$state'";
$result = $conn->query($sql);
$cities = [];
while ($row = $result->fetch_assoc()) {
    $cities[] = $row['city'];
}

echo json_encode($cities);
$conn->close();
?>
