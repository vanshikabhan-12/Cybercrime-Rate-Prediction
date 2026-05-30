<?php
// Connect to the database
$host = 'localhost:3307';
$dbname = 'cybercrime_db';
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['state'])) {
    // Get the selected state from the request
    $state = $_POST['state'];

    // Query the cities based on the selected state
    $query = "SELECT DISTINCT city FROM cybercrime_db WHERE state='$state'";
    $result = $conn->query($query);

    // Check if any cities were found
    if ($result->num_rows > 0) {
        echo '<option value="">Select City</option>'; // Add default option
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . $row['city'] . '">' . $row['city'] . '</option>';
        }
    } else {
        echo '<option value="">No cities available</option>';
    }
} else {
    echo '<option value="">Select State First</option>';
}

$conn->close();
?>
