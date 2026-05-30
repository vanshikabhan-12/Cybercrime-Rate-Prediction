document.addEventListener('DOMContentLoaded', function() {
    fetchTopCities();
});

function fetchTopCities() {
    fetch('fetch_top_cities.php')
    .then(response => response.json())
    .then(data => {
        // Display top cities with highest cybercrime rates
        const topCitiesList = document.getElementById('top-cities-list');
        data.topCities.forEach(city => {
            const listItem = document.createElement('li');
            listItem.textContent = `${city.city}: ${city.total_crimes} Crimes`;
            topCitiesList.appendChild(listItem);
        });

        // Display top cities with lowest cybercrime rates
        const lowestCitiesList = document.getElementById('lowest-cities-list');
        data.lowestCities.forEach(city => {
            const listItem = document.createElement('li');
            listItem.textContent = `${city.city}: ${city.total_crimes} Crimes`;
            lowestCitiesList.appendChild(listItem);
        });

        // Optionally, create a chart for visualization
        createChart(data.topCities, data.lowestCities);
    })
    .catch(error => console.error('Error fetching data:', error));
}

function createChart(topCities, lowestCities) {
    const ctx = document.getElementById('crime-chart').getContext('2d');

    const topCitiesNames = topCities.map(city => city.city);
    const topCitiesCrimeRates = topCities.map(city => city.total_crimes);

    const lowestCitiesNames = lowestCities.map(city => city.city);
    const lowestCitiesCrimeRates = lowestCities.map(city => city.total_crimes);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: topCitiesNames.concat(lowestCitiesNames),
            datasets: [{
                label: 'Reported Crimes',
                data: topCitiesCrimeRates.concat(lowestCitiesCrimeRates),
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}
