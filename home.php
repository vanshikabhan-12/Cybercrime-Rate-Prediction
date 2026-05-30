<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crime Prediction System</title>
    <link rel="stylesheet" href="home1.css">
</head>
<body>

<header>
    <div class="wrapper">
        <!-- User Profile Section -->
        <div class="user-menu" onclick="toggleDropdown()">
            <span class="username">Welcome, User</span>
            <button class="dropdown-button">▼</button>
            <div class="dropdown-content">
                <a href="profile.php">Profile</a>
                
                <a href="before login.html">Logout</a>
            </div>
        </div>

        <!-- Navigation Menu -->
        <ul class="nav-area">
            <li><a href="home.php">Home</a></li>
            <li><a href="map.php">MapIt</a></li>
            <li><a href="crime.php">Crime Stats</a></li>
            <li><a href="social.html">Fake Account Detector</a></li>
            <li><a href="top_cities.php">Top city</a></li>
            <li><a href="cyberbot.html">Cyber bot</a></li>
        </ul>
    </div>
</header>

<!-- Welcome Section -->
<section class="welcome-section">
    <h1>Welcome to the Crime Prediction System</h1>
    <p>This system helps predict crime rates, provides an interactive map for crime visualization, and detects fake accounts using AI-powered algorithms.</p>
</section>

<!-- Cards Section -->
<div class="cards-container">
    <div class="card">
        <h3>Crime Rate Prediction</h3>
        <p>Predict the crime rate of cities based on historical data and AI models.</p>
        <a href="crime.php" class="btn">Learn More</a>
    </div>

    <div class="card">
        <h3>Interactive Crime Map</h3>
        <p>Visualize crime-prone areas on an interactive map with real-time data.</p>
        <a href="map.php" class="btn">Learn More</a>
    </div>

    <div class="card">
        <h3>Fake Account Detector</h3>
        <p>Detect fake social media accounts using AI and machine learning models.</p>
        <a href="social.html" class="btn">Learn More</a>
    </div>

    <div class="card">
        <h3>Top Cities with Crime</h3>
        <p>Discover the top cities with the highest and lowest crime rates.</p>
        <a href="top_cities.php" class="btn">Learn More</a>
    </div>
</div>

<!-- Inline JavaScript for Dropdown -->
<script>
    function toggleDropdown() {
        let userMenu = document.querySelector(".user-menu");
        userMenu.classList.toggle("active");
    }
</script>

</body>
</html>
