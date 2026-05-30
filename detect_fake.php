<?php
$servername = "localhost:3307";
$username = "root";  // Change if needed
$password = "";  // Change if needed
$dbname = "cybercrime_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['username'])) {
    $username = $_GET['username'];
    $sql = "SELECT * FROM insta WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $followers = $row["followers"];
        $following = $row["following"];
        $profile_info = $row["profile_info"];
        $photo_link = $row["photo_link"];

        // Fake Account Detection Logic
        $is_fake = ($followers < 50 && $following > 1000) || empty($profile_info);

        echo json_encode([
            "username" => $row["username"],
            "followers" => $followers,
            "following" => $following,
            "profile_info" => $profile_info,
            "photo_link" => $photo_link,
            "is_fake" => $is_fake
        ]);
    } else {
        echo json_encode(["error" => "No user found"]);
    }
} else {
    echo json_encode(["error" => "Username not provided"]);
}

$conn->close();
?>
