<?php
include 'db_connection.php';

$result = $conn->query("SELECT DISTINCT state FROM cybercrime_db");
$states = [];

while ($row = $result->fetch_assoc()) {
    $states[] = $row['state'];
}

echo json_encode($states);

$conn->close();
?>
