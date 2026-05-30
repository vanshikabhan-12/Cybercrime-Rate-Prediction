<?php
// Include your database connection
include('db_connection.php');

// Prepare SQL queries to get the top 5 cities with the highest and lowest cybercrime rates
$topCitiesQuery = "
    SELECT city, state, SUM(reported_crimes) AS total_crimes
    FROM cybercrime_db
    GROUP BY city, state
    ORDER BY total_crimes DESC
    LIMIT 5";
$topCitiesResult = $conn->query($topCitiesQuery);
$topCities = [];
while ($row = $topCitiesResult->fetch_assoc()) {
    $topCities[] = $row;
}

// Fetch the top 5 cities with the lowest crime rates
$lowestCitiesQuery = "
    SELECT city, state, SUM(reported_crimes) AS total_crimes
    FROM cybercrime_db
    GROUP BY city, state
    ORDER BY total_crimes ASC
    LIMIT 5";
$lowestCitiesResult = $conn->query($lowestCitiesQuery);
$lowestCities = [];
while ($row = $lowestCitiesResult->fetch_assoc()) {
    $lowestCities[] = $row;
}

$conn->close();

// Return all the data as JSON
echo json_encode([
    'topCities' => $topCities,
    'lowestCities' => $lowestCities
]);
?>
