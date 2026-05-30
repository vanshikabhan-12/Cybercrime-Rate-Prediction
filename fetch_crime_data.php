<?php
// Database connection
$host = 'localhost:3307';
$dbname = 'cybercrime_db';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch crime data based on state and city
if (isset($_POST['state']) && isset($_POST['city'])) {
    $state = $conn->real_escape_string($_POST['state']);
    $city = $conn->real_escape_string($_POST['city']);

    $query = "SELECT crime_rate, solved_cases, unsolved_cases FROM cybercrime_db WHERE state='$state' AND city='$city' ORDER BY year DESC LIMIT 1";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
    } else {
        $data = ['crime_rate' => 'No data', 'solved_cases' => 'No data', 'unsolved_cases' => 'No data'];
    }

    echo json_encode($data);
}
?>
