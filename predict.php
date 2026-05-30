<?php
$host = 'localhost';
$dbname = 'cybercrime_db';
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

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
        
        // ✅ Fix: Ensure values are not null
        $solved_cases = !empty($row['solved_cases']) ? floatval($row['solved_cases']) : 0;
        $unsolved_cases = !empty($row['unsolved_cases']) ? floatval($row['unsolved_cases']) : 0;
    }
}

$actual_crime_rate = ($population > 0) ? ($reported_cases / $population) * 100000 : "No data available";

function euclidean_distance($point1, $point2) {
    return sqrt(pow($point1[0] - $point2[0], 2) + pow($point1[1] - $point2[1], 2));
}

function knn_predict($data, $year, $population, $reported_cases, $k = 3) {
    $distances = [];
    foreach ($data as $row) {
        $crime_rate = (floatval($row['reported_cases']) / floatval($row['population'])) * 100000;
        $distances[] = [
            'distance' => euclidean_distance([$row['year'], $row['population']], [$year, $population]),
            'crime_rate' => $crime_rate
        ];
    }

    usort($distances, fn($a, $b) => $a['distance'] <=> $b['distance']);
    $nearest_neighbors = array_slice($distances, 0, $k);

    $sum = 0;
    foreach ($nearest_neighbors as $neighbor) {
        $sum += $neighbor['crime_rate'];
    }

    return ($k > 0) ? $sum / $k : 0;
}

$predicted_crime_rate = !empty($data) ? knn_predict($data, $year, $population, $reported_cases) : "No data available";

echo json_encode([
    'actual_crime_rate' => $actual_crime_rate,
    'predicted_crime_rate' => $predicted_crime_rate,
    'solved_cases' => $solved_cases,
    'unsolved_cases' => $unsolved_cases
]);
?>
