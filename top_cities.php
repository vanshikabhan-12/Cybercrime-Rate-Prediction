<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cybercrime_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch city-wise crime data
$sql = "SELECT city, SUM(solved_cases) AS solved_cases, SUM(unsolved_cases) AS unsolved_cases, population 
        FROM cybercrime_db 
        GROUP BY city";

$result = $conn->query($sql);

$crime_rates = [];

while ($row = $result->fetch_assoc()) {
    $total_crimes = $row['solved_cases'] + $row['unsolved_cases'];
    $population = $row['population'];

    if ($population > 0) {
        $crime_rate = ($total_crimes / $population) * 1000;
    } else {
        $crime_rate = 0;
    }

    $crime_rates[] = [
        'city' => $row['city'],
        'crime_rate' => round($crime_rate, 2)
    ];
}

// Sort crime rates
usort($crime_rates, function ($a, $b) {
    return $b['crime_rate'] <=> $a['crime_rate'];
});

// Get top 5 highest and lowest crime rate cities
$highest_cities = array_slice($crime_rates, 0, 5);
$lowest_cities = array_slice(array_reverse($crime_rates), 0, 5);

$years = $conn->query("SELECT DISTINCT year FROM cybercrime_db ORDER BY year DESC");
$crime_types = $conn->query("SELECT DISTINCT crime_type FROM cybercrime_db");

// Fetch data based on filters
$selected_year = isset($_GET['year']) ? $_GET['year'] : date('Y');
$selected_crime = isset($_GET['crime_type']) ? $_GET['crime_type'] : 'All';

$query = "SELECT city, SUM(reported_cases) AS total_cases, SUM(population) AS total_population
          FROM cybercrime_db 
          WHERE year = '$selected_year'";

if ($selected_crime !== 'All') {
    $query .= " AND crime_type = '$selected_crime'";
}

$query .= " GROUP BY city";

$result = $conn->query($query);

$crime_data = [];
while ($row = $result->fetch_assoc()) {
    if ($row['total_population'] > 0) {
        $crime_rate = ($row['total_cases'] / $row['total_population']) * 100000;
        $crime_data[$row['city']] = $crime_rate;
    }
}

arsort($crime_data);
$top_cities = array_slice($crime_data, 0, 5, true);
$low_cities = array_slice($crime_data, -5, 5, true);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crime Rate Visualization</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body {
            background-color: #121212;
            color: #e0e0e0;
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 20px;
        }
        .chart-container {
            width: 90%;
            max-width: 1200px;
            height: 600px;
            margin: 30px auto;
            padding: 20px;
            background-color: #1e1e1e;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(255, 255, 255, 0.2);
        }
        canvas {
            width: 100%;
            height: 100%;
        }
        select, button {
            background-color: #333;
            color: #fff;
            border: 1px solid #555;
            padding: 8px;
            margin: 5px;
            border-radius: 5px;
        }
        button:hover {
            background-color: #444;
        }
        /* Header Section */
header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 30px;
    background: rgba(0, 0, 0, 0.8);
}

/* Wrapper for aligning profile and navigation */
.wrapper {
    display: flex;
    align-items: center;
    width: 100%;
}

/* User profile section */
.user-menu {
    display: flex;
    align-items: center;
    position: relative;
    cursor: pointer;
}

.username {
    margin-right: 10px;
    font-weight: bold;
}

/* Dropdown Button */
.dropdown-button {
    background: none;
    border: none;
    color: white;
    font-size: 16px;
    cursor: pointer;
}

/* Dropdown Content */
.dropdown-content {
    display: none;
    position: absolute;
    background: #444;
    right: 0;
    top: 35px;
    min-width: 150px;
    text-align: left;
    border-radius: 5px;
    box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.3);
}

.dropdown-content a {
    display: block;
    color: white;
    padding: 10px;
    text-decoration: none;
}

.dropdown-content a:hover {
    background-color: #555;
}

/* Show dropdown when clicked */
.user-menu.active .dropdown-content {
    display: block;
}

/* Navigation Bar */
.nav-area {
    list-style: none;
    display: flex;
    margin-left: auto; /* Pushes nav links to the right */
}

.nav-area li {
    margin-left: 20px;
}

.nav-area a {
    color: white;
    text-decoration: none;
    font-size: 16px;
    font-weight: 600;
}

.nav-area a:hover {
    color: #007bff;
}

    </style>
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


    <h2>Top 5 Cities with Highest Crime Rates</h2>
    <div class="chart-container">
        <canvas id="highestCrimeChart"></canvas>
    </div>

    <h2>Top 5 Cities with Lowest Crime Rates</h2>
    <div class="chart-container">
        <canvas id="lowestCrimeChart"></canvas>
    </div>

    <script>
        var commonOptions = {
            indexAxis: 'y',
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { color: "white", font: { size: 14 } },
                    
                },
                y: {
                    ticks: { color: "white", font: { size: 14 } },
                    
                }
            },
            plugins: {
                legend: { labels: { color: "white", font: { size: 14 } } }
            }
        };

        var highestCrimeCtx = document.getElementById('highestCrimeChart').getContext('2d');
        new Chart(highestCrimeCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($highest_cities, 'city')); ?>,
                datasets: [{
                    label: 'Crime Rate (per 1000 people)',
                    data: <?php echo json_encode(array_column($highest_cities, 'crime_rate')); ?>,
                    backgroundColor: 'red'
                }]
            },
            options: commonOptions
        });

        var lowestCrimeCtx = document.getElementById('lowestCrimeChart').getContext('2d');
        new Chart(lowestCrimeCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($lowest_cities, 'city')); ?>,
                datasets: [{
                    label: 'Crime Rate (per 1000 people)',
                    data: <?php echo json_encode(array_column($lowest_cities, 'crime_rate')); ?>,
                    backgroundColor: 'green'
                }]
            },
            options: commonOptions
        });
    </script>

    <h2>Top 5 Cities with Highest & Lowest Crime Rates</h2>

    <form method="GET">
        <label>Select Year:</label>
        <select name="year">
            <?php while ($row = $years->fetch_assoc()) {
                echo "<option value='{$row['year']}' " . ($selected_year == $row['year'] ? 'selected' : '') . ">{$row['year']}</option>";
            } ?>
        </select>

        <label>Select Crime Type:</label>
        <select name="crime_type">
            <option value="All" <?= $selected_crime == 'All' ? 'selected' : '' ?>>All</option>
            <?php while ($row = $crime_types->fetch_assoc()) {
                echo "<option value='{$row['crime_type']}' " . ($selected_crime == $row['crime_type'] ? 'selected' : '') . ">{$row['crime_type']}</option>";
            } ?>
        </select>

        <button type="submit">Filter</button>
    </form>

    <div class="chart-container">
        <canvas id="crimeChart"></canvas>
    </div>

    <script>
        var ctx = document.getElementById('crimeChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_merge(array_keys($top_cities), array_keys($low_cities))) ?>,
                datasets: [{
                    label: 'Crime Rate (per 100,000 people)',
                    data: <?= json_encode(array_merge(array_values($top_cities), array_values($low_cities))) ?>,
                    backgroundColor: [...Array(5).fill("red"), ...Array(5).fill("green")]
                }]
            },
            options: commonOptions
        });
    </script>
<!-- Inline JavaScript for Dropdown -->
<script>
    function toggleDropdown() {
        let userMenu = document.querySelector(".user-menu");
        userMenu.classList.toggle("active");
    }
</script>
</body>
</html>
