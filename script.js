function fetchData() {
    const state = document.getElementById('state-dropdown').value;
    const city = document.getElementById('city-dropdown').value;
    const crimeType = document.getElementById('cybercrime-type-dropdown').value;
    const year = document.getElementById('year-dropdown').value;

    // Ensure there is at least one filter selected
    if (!state && !city && !crimeType && !year) {
        alert("Please select at least one filter.");
        return;
    }

    // Send a POST request to fetch_data.php with the selected filters
    fetch('fetch_data.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ state, city, crimeType, year })
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            alert(data.message);  // Display the message if no data is found
        } else {
            // Display the fetched data in the UI
            document.getElementById('predicted-crime-rate').innerText = data.predicted_crime_rate || "N/A";
            document.getElementById('solved-cases').innerText = data.solved_cases || "N/A";
            document.getElementById('unsolved-cases').innerText = data.unsolved_cases || "N/A";

            // Update the chart with the data
            updateChart(data.chartData);
        }
    })
    .catch(error => {
        console.error('Error fetching data:', error);
    });
}

function updateChart(data) {
    const ctx = document.getElementById('crime-chart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Reported Crimes',
                data: data.values,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
            }],
        },
        options: {
            responsive: true,
        },
    });
}
