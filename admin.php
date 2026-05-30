<?php
// Database connection
$servername = "localhost:3307";
$username = "root";
$password = ""; // Default password for XAMPP
$dbname = "cybercrime_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Delete Request
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('User deleted successfully');</script>";
    } else {
        echo "<script>alert('Error deleting user');</script>";
    }
    $stmt->close();
}

// Handle Edit Request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $userId = $_POST['userId'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];

    $sql = "UPDATE users SET userId = ?, email = ?, contact = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $userId, $email, $contact, $id);

    if ($stmt->execute()) {
        echo "<script>alert('User updated successfully');</script>";
    } else {
        echo "<script>alert('Error updating user');</script>";
    }
    $stmt->close();
}

// Fetch all registered users
$sql = "SELECT id, userId, email, contact, created_at FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: linear-gradient(rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.8)), url("https://wallpapercave.com/wp/wp6781415.jpg");
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
            color: white;
        }
        th {
            background-color: black;
        }
        h2 {
            margin-bottom: 20px;
            color: white;
        }
        .form-inline input {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <h2>Admin Panel</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Registered At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['userId']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['contact']}</td>
                        <td>{$row['created_at']}</td>
                        <td>
                            <a href='?edit={$row['id']}'>Edit</a> | 
                            <a href='?delete={$row['id']}' onclick='return confirm(\"Are you sure you want to delete this user?\")'>Delete</a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No users found</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <?php
    // If edit mode is triggered, show the edit form
    if (isset($_GET['edit'])) {
        $id = $_GET['edit'];
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        ?>
        <h3>Edit User</h3>
        <form method="POST" class="form-inline">
            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
            <input type="text" name="userId" value="<?php echo $user['userId']; ?>" required>
            <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
            <input type="text" name="contact" value="<?php echo $user['contact']; ?>" required>
            <button type="submit" name="edit">Update</button>
        </form>
        <?php
        $stmt->close();
    }
    ?>

</body>
</html>

<?php
$conn->close();
?>
