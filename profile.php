<?php
session_start();

// Database connection
$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "cybercrime_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user ID from session (assuming the user is logged in)
$user_id = $_SESSION['userId'];

// Fetch user data from database
$sql = "SELECT * FROM users WHERE userId = '$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Initialize variables
$profile_updated = false;  // Flag to check if profile was updated

// Profile update or OTP flow
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // If user wants to change userId, email, or contact
    if (isset($_POST['update_profile'])) {
        $new_userId = $_POST['userId'];
        $new_email = $_POST['email'];
        $new_contact = $_POST['contact'];

        // Update user data in database
        $update_sql = "UPDATE users SET userId='$new_userId', email='$new_email', contact='$new_contact' WHERE userId='$user_id'";
        if ($conn->query($update_sql) === TRUE) {
            $profile_updated = true;  // Set flag to show notification
            // Update session data to reflect the change
            $_SESSION['user_id'] = $new_userId;
        } else {
            echo "Error updating profile: " . $conn->error;
        }
    }

    // If user forgot their password and wants to receive OTP
    if (isset($_POST['send_otp'])) {
        // Generate OTP
        $otp = rand(100000, 999999);
        
        // Save OTP in session for verification
        $_SESSION['otp'] = $otp;

        // Send OTP via mail()
        $to = $user['email'];
        $subject = 'Your OTP for Password Reset';
        $message = 'Your OTP is: ' . $otp;
        $headers = 'From: your-email@example.com' . "\r\n" .
            'Reply-To: your-email@example.com' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        
        if (mail($to, $subject, $message, $headers)) {
            echo "OTP sent to your email.";
        } else {
            echo "Error sending OTP.";
        }
    }

    // If user enters OTP to change password
    if (isset($_POST['otp']) && isset($_POST['new_password'])) {
        $entered_otp = $_POST['otp'];
        $new_password = $_POST['new_password'];

        if ($_SESSION['otp'] == $entered_otp) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_sql = "UPDATE users SET password='$hashed_password' WHERE userId='$user_id'";
            if ($conn->query($update_sql) === TRUE) {
                echo "Password updated successfully.";
            } else {
                echo "Error updating password: " . $conn->error;
            }
            // Clear OTP from session after successful change
            unset($_SESSION['otp']);
        } else {
            echo "Incorrect OTP.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="profile.css">
    <style>
        /* Style for the profile updated notification */
        .notification {
            display: none;
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            position: fixed;
            top: 20px;
            right: 20px;
            border-radius: 5px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <h2>Edit Your Profile</h2>

        <!-- Form to change userId, email, and contact -->
        <form method="POST" id="profile-form">
            <label for="userId">User ID:</label>
            <input type="text" id="userId" name="userId" value="<?php echo $user['userId']; ?>" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>
            
            <label for="contact">Contact:</label>
            <input type="text" id="contact" name="contact" value="<?php echo $user['contact']; ?>" required>

            <button type="submit" name="update_profile">Update Profile</button>
        </form>

        <!-- Forgot Password Link -->
        <form method="POST">
            <button type="submit" name="send_otp">Forgot Password?</button>
        </form>

        <!-- Form to enter OTP and set new password -->
        <?php if (isset($_POST['send_otp'])): ?>
            <h3>Enter OTP to Reset Password</h3>
            <form method="POST">
                <label for="otp">Enter OTP:</label>
                <input type="text" id="otp" name="otp" required>
                
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required>

                <button type="submit">Set New Password</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Profile Updated Notification -->
    <div id="notification" class="notification">
        Profile updated successfully!
        <button onclick="closeNotification()">OK</button>
    </div>

    <script>
        // Show the profile updated notification after form submission
        <?php if ($profile_updated): ?>
            document.getElementById("notification").style.display = "block";
        <?php endif; ?>

        // Function to close the notification when user clicks "OK"
        function closeNotification() {
            document.getElementById("notification").style.display = "none";
            document.getElementById("profile-form").submit();  // Automatically submit the form to update the profile
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
