<?php
// Connect to the database
$conn = new mysqli("localhost:3307", "root", "", "cybercrime_db");

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $userId = $_POST['userId']; // Matches the name attribute in your form
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Prepare the SQL query to insert data
    $stmt = $conn->prepare("INSERT INTO users (userId, email, contact, password) VALUES (?, ?, ?, ?)");
    if ($stmt === false) {
        die("MySQL prepare error: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("ssss", $userId, $email, $contact, $password);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect to login page on successful registration
        echo "<script>alert('Registered successfully! Redirecting to login page.');</script>";
        echo "<script>window.location.href='login.html';</script>";
    } else {
        // Show an error message if registration fails
        echo "<script>alert('Registration failed: " . $stmt->error . "');</script>";
        echo "<script>window.location.href='register.html';</script>";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>