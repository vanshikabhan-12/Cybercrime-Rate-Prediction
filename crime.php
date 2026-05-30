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

// Fetch unique states, crime types, and years
$states = $conn->query("SELECT DISTINCT state FROM cybercrime_db");
$crime_types = $conn->query("SELECT DISTINCT crime_type FROM cybercrime_db");
$years = $conn->query("SELECT DISTINCT year FROM cybercrime_db ORDER BY year DESC");

// Handle AJAX request for cities based on selected state
if (isset($_POST['fetch_cities']) && isset($_POST['state'])) {
    $state = $_POST['state'];
    $cities = $conn->query("SELECT DISTINCT city FROM cybercrime_db WHERE state='$state'");
    
    $city_options = "<option value=''>Select City</option>";
    while ($row = $cities->fetch_assoc()) {
        $selected = ($_POST['selected_city'] == $row['city']) ? "selected" : "";
        $city_options .= "<option value='" . $row['city'] . "' $selected>" . $row['city'] . "</option>";
    }
    echo $city_options;
    exit;
}

// Function to calculate Euclidean distance
function euclidean_distance($point1, $point2) {
    return sqrt(pow($point1[0] - $point2[0], 2) + pow($point1[1] - $point2[1], 2));
}

// KNN Prediction Function
function knn_predict($data, $year, $population, $k = 3) {
    $distances = [];

    // Calculate mean and std deviation for normalization
    $years = array_column($data, 'year');
    $populations = array_column($data, 'population');

    $mean_year = array_sum($years) / count($years);
    $std_year = standard_deviation($years);

    $mean_population = array_sum($populations) / count($populations);
    $std_population = standard_deviation($populations);

    // Normalize input year and population
    $normalized_input = [
        ($year - $mean_year) / ($std_year ?: 1),
        ($population - $mean_population) / ($std_population ?: 1)
    ];

    foreach ($data as $row) {
        $normalized_row = [
            ($row['year'] - $mean_year) / ($std_year ?: 1),
            ($row['population'] - $mean_population) / ($std_population ?: 1)
        ];
        $crime_rate = (floatval($row['reported_cases']) / floatval($row['population'])) * 100000;

        $distances[] = [
            'distance' => euclidean_distance($normalized_row, $normalized_input),
            'crime_rate' => $crime_rate
        ];
    }

    usort($distances, function ($a, $b) {
        return $a['distance'] <=> $b['distance'];
    });

    $nearest_neighbors = array_slice($distances, 0, $k);

    $sum = 0;
    foreach ($nearest_neighbors as $neighbor) {
        $sum += $neighbor['crime_rate'];
    }

    return ($k > 0) ? $sum / $k : 0;
}

function standard_deviation($array) {
    $mean = array_sum($array) / count($array);
    $variance = array_reduce($array, function ($carry, $item) use ($mean) {
        return $carry + pow($item - $mean, 2);
    }, 0) / count($array);
    return sqrt($variance);
}


// Auto-fill state and city from URL if redirected (e.g., from `map.php`)
$autofill_state = isset($_GET['state']) ? $_GET['state'] : "";
$autofill_city = isset($_GET['city']) ? $_GET['city'] : "";

// Handle form submission for crime prediction
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['fetch_cities'])) {
    $state = $_POST['state'];
    $city = $_POST['city'];
    $crime_type = $_POST['crime_type'];
    $year = $_POST['year'];

    $query = "SELECT * FROM cybercrime_db WHERE state='$state' AND city='$city' AND crime_type='$crime_type'";
    $result = $conn->query($query);

    $data = [];
    $population = 0;
    $reported_cases = 0;
    $solved_cases = 0;
    $unsolved_cases = 0;

    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'year' => $row['year'],
            'population' => floatval($row['population']),
            'reported_cases' => floatval($row['reported_cases'])
        ];

        if ($row['year'] == $year) {
            $population = floatval($row['population']);
            $reported_cases = floatval($row['reported_cases']);
            $solved_cases = floatval($row['solved_cases']);
            $unsolved_cases = floatval($row['unsolved_cases']);
        }
    }

    $actual_crime_rate = ($population > 0) ? ($reported_cases / $population) * 100000 : "No data available";
    $predicted_crime_rate = !empty($data) ? knn_predict($data, $year, $population) : "No data available";

    echo json_encode([
        'actual_crime_rate' => $actual_crime_rate,
        'predicted_crime_rate' => $predicted_crime_rate,
        'solved_cases' => $solved_cases,
        'unsolved_cases' => $unsolved_cases
    ]);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cyber Crime Prediction</title>
    <link rel="stylesheet" href="crime.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
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
    <h2 style="text-align: center;">Cyber Crime Rate Prediction</h2>
    
    <form id="predictForm">
        <label>State:</label>
        <select name="state" id="state" required>
            <option value="">Select State</option>
            <?php while ($row = $states->fetch_assoc()) { 
                $selected = ($row['state'] == $autofill_state) ? "selected" : "";
                echo "<option value='" . $row['state'] . "' $selected>" . $row['state'] . "</option>"; 
            } ?>
        </select><br>
        
        <label>City:</label>
        <select name="city" id="city" required>
            <option value="">Select City</option>
        </select><br>
        
        <label>Crime Type:</label>
        <select name="crime_type" id="crime_type" required>
            <option value="">Select Crime Type</option>
            <?php while ($row = $crime_types->fetch_assoc()) { 
                echo "<option value='" . $row['crime_type'] . "'>" . $row['crime_type'] . "</option>"; 
            } ?>
        </select><br>
        
        <label>Year:</label>
        <select name="year" id="year" required>
            <option value="">Select Year</option>
            <?php while ($row = $years->fetch_assoc()) { 
                echo "<option value='" . $row['year'] . "'>" . $row['year'] . "</option>"; 
            } ?>
        </select><br>
        
        <button type="submit">Predict</button>
    </form>

    <div class="data-display">
        <div class="row">
            <div class="card">
                <h3>Actual Crime Rate</h3>
                <p id="actual-crime-rate">---</p>
            </div>
            <div class="card">
                <h3>Predicted Crime Rate</h3>
                <p id="predicted-crime-rate">---</p>
            </div>
        </div>
        <div class="row">
            <div class="card">
                <h3>Solved Cases</h3>
                <p id="solved-cases">---</p>
            </div>
            <div class="card">
                <h3>Unsolved Cases</h3>
                <p id="unsolved-cases">---</p>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function(){
        function loadCities(state, selectedCity) {
            $.post('', { fetch_cities: 1, state: state, selected_city: selectedCity }, function(response) {
                $('#city').html(response);
            });
        }

        $('#state').change(function(){
            loadCities($(this).val(), '');
        });

        if ('<?php echo $autofill_state; ?>') {
            loadCities('<?php echo $autofill_state; ?>', '<?php echo $autofill_city; ?>');
        }

        // Handle form submission via AJAX
        $('#predictForm').submit(function(e) {
            e.preventDefault(); // Prevent form from reloading the page

            $.ajax({
                type: 'POST',
                url: '', 
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    $('#actual-crime-rate').text(response.actual_crime_rate);
                    $('#predicted-crime-rate').text(response.predicted_crime_rate);
                    $('#solved-cases').text(response.solved_cases);
                    $('#unsolved-cases').text(response.unsolved_cases);
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                }
            });
        });
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
