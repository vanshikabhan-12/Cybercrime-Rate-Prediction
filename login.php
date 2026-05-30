<?php
session_start();

// Database connection
$conn = new mysqli("localhost:3307", "root", "", "cybercrime_db");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define admin credentials (predefined)
$adminId = "admin";
$adminPassword = "admin123"; // Plain-text password for admin (you can hash this for better security)

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_POST['userId'];
    $password = $_POST['password'];

    // Check if the user is an admin
    if ($userId === $adminId && $password === $adminPassword) {
        $_SESSION['userId'] = $userId;  // Store admin ID in session
        $_SESSION['role'] = 'admin';   // Add a role for further checks (optional)
        
        // Redirect to admin dashboard
        header("Location: admin_home.html");
        exit; // Stop further execution
    }

    // If not an admin, check the users table for regular users
    $stmt = $conn->prepare("SELECT password FROM users WHERE userId = ?");
    
    if ($stmt === false) {
        die("MySQL prepare error: " . $conn->error);
    }

    $stmt->bind_param("s", $userId); // Bind the userId
    $stmt->execute();
    $stmt->bind_result($hashed_password);

    // Check if user found and password is correct
    if ($stmt->fetch() && password_verify($password, $hashed_password)) {
        $_SESSION['userId'] = $userId;  // Store userId in session

        // Redirect to the home page
        header("Location: home.php");
        exit;
    } else {
        // Incorrect credentials
        echo "<script>alert('User not registered or incorrect password!');</script>";
        echo "<script>window.location.href='login.html';</script>"; // Redirect back to login page
    }

    // Close database connection
    $stmt->close();
    $conn->close();
}
?>
