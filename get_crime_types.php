<?php
include 'db_connection.php';

$city = $_GET['city'];

// Fetch crime types for the selected city
$sql = "SELECT DISTINCT crime_type FROM cybercrime_db WHERE city='$city'";
$result = $conn->query($sql);
$crimeTypes = [];
while ($row = $result->fetch_assoc()) {
    $crimeTypes[] = $row['crime_type'];
}

echo json_encode($crimeTypes);
$conn->close();
?>
