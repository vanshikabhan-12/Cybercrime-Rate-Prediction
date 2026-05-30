<?php 
include 'db_connection.php'; // Include your database connection

// Fetch distinct years for the year dropdown
function getYears($conn) {
    $sql = "SELECT DISTINCT year FROM cybercrime_db";
    $result = $conn->query($sql);
    $years = [];
    while ($row = $result->fetch_assoc()) {
        $years[] = $row['year'];
    }
    return $years;
}

// Fetch available states for the state dropdown
function getStates($conn) {
    $sql = "SELECT DISTINCT state FROM cybercrime_db";
    $result = $conn->query($sql);
    $states = [];
    while ($row = $result->fetch_assoc()) {
        $states[] = $row['state'];
    }
    return $states;
}

$states = getStates($conn);
$years = getYears($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cybercrime Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="dashboard-container">
        <!-- Filter Section -->
        <div class="filter-section">
            <div>
                <label for="state-dropdown">Select State:</label>
                <select id="state-dropdown" onchange="fetchCities()">
                    <option value="">Select State</option>
                    <?php foreach ($states as $state): ?>
                        <option value="<?php echo $state; ?>"><?php echo $state; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="city-dropdown">Select City:</label>
                <select id="city-dropdown" onchange="fetchCrimeTypes()">
                    <option value="">Select City</option>
                </select>
            </div>

            <div>
                <label for="crime-type-dropdown">Select Crime Type:</label>
                <select id="crime-type-dropdown">
                    <option value="">Select Crime Type</option>
                </select>
            </div>

            <div>
                <label for="year-dropdown">Select Year:</label>
                <select id="year-dropdown">
                    <option value="">Select Year</option>
                    <?php foreach ($years as $year): ?>
                        <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <button id="fetch-button" onclick="fetchData()">PREDICT</button>
            </div>
        </div>

        <!-- Data Display Section -->
        <div class="data-display">
            <div class="card">
                <h3>Predicted Crime Rate</h3>
                <p id="predicted-crime-rate">Loading...</p>
            </div>
            <div class="card">
                <h3>Solved Cases</h3>
                <p id="solved-cases">Loading...</p>
            </div>
            <div class="card">
                <h3>Unsolved Cases</h3>
                <p id="unsolved-cases">Loading...</p>
            </div>
        </div>

        <!-- Visualization Section -->
        <div class="visualization">
            <div class="chart-header">Bar Chart: Reported Crimes Over Years</div>
            <canvas id="bar-chart"></canvas>

            <div class="chart-header">Pie Chart: Solved vs Unsolved Cases</div>
            <canvas id="pie-chart"></canvas>
        </div>

    </div>

    <footer>
        <p>&copy; 2025 Cybercrime Dashboard. All rights reserved.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
    fetchStates(); // Populate states on page load
});

// Fetch cities based on the selected state
function fetchCities() {
    const state = document.getElementById('state-dropdown').value;
    if (!state) return;

    fetch(`get_cities.php?state=${state}`)
        .then(response => response.json())
        .then(cities => {
            const cityDropdown = document.getElementById('city-dropdown');
            cityDropdown.innerHTML = '<option value="">Select City</option>';
            cities.forEach(city => {
                const option = document.createElement('option');
                option.value = city;
                option.textContent = city;
                cityDropdown.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error fetching cities:', error);
            alert('Error fetching cities');
        });
}

// Fetch crime types based on the selected city
function fetchCrimeTypes() {
    const city = document.getElementById('city-dropdown').value;
    if (!city) return;

    fetch(`get_crime_types.php?city=${city}`)
        .then(response => response.json())
        .then(crimeTypes => {
            const crimeTypeDropdown = document.getElementById('crime-type-dropdown');
            crimeTypeDropdown.innerHTML = '<option value="">Select Crime Type</option>';
            crimeTypes.forEach(type => {
                const option = document.createElement('option');
                option.value = type;
                option.textContent = type;
                crimeTypeDropdown.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error fetching crime types:', error);
            alert('Error fetching crime types');
        });
}

// Fetch data based on selected filters
function fetchData() {
    const state = document.getElementById('state-dropdown').value;
    const city = document.getElementById('city-dropdown').value;
    const crimeType = document.getElementById('crime-type-dropdown').value;
    const year = document.getElementById('year-dropdown').value;

    // Check if all fields are selected
    if (!state || !city || !crimeType || !year) {
        alert('Please select all fields before predicting.');
        return;
    }

    const requestData = { state, city, crimeType, year };

    console.log('Sending request:', requestData);

    // Send data using fetch API
    fetch('fetch_crime_data.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(requestData),
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);

        if (data.success) {
            document.getElementById('predicted-crime-rate').textContent = data.predicted_crime_rate;
            document.getElementById('solved-cases').textContent = data.solved_cases;
            document.getElementById('unsolved-cases').textContent = data.unsolved_cases;
            createCharts(data.chartData);  // Create charts with the data
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error fetching prediction data:', error);
        alert('Failed to fetch prediction data.');
    });
}

// Function to create charts
function createCharts(chartData) {
    const barChartCanvas = document.getElementById('bar-chart').getContext('2d');
    const pieChartCanvas = document.getElementById('pie-chart').getContext('2d');

    // Bar Chart (Reported Crimes Over Years)
    new Chart(barChartCanvas, {
        type: 'bar',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Reported Crimes',
                data: chartData.values,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Pie Chart (Solved vs Unsolved Cases)
    new Chart(pieChartCanvas, {
        type: 'pie',
        data: {
            labels: ['Solved Cases', 'Unsolved Cases'],
            datasets: [{
                data: [chartData.solvedCases, chartData.unsolvedCases],
                backgroundColor: ['rgba(75, 192, 192, 0.2)', 'rgba(255, 99, 132, 0.2)'],
                borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)'],
                borderWidth: 1
            }]
        }
    });
}
    </script>
</body>
</html>
