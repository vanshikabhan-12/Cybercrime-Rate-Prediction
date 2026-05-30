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
            listItem.textContent = `${city.city}, ${city.state}: ${city.total_crimes} Crimes`;
            topCitiesList.appendChild(listItem);
        });

        // Display top cities with lowest cybercrime rates
        const lowestCitiesList = document.getElementById('lowest-cities-list');
        data.lowestCities.forEach(city => {
            const listItem = document.createElement('li');
            listItem.textContent = `${city.city}, ${city.state}: ${city.total_crimes} Crimes`;
            lowestCitiesList.appendChild(listItem);
        });

        // Create charts for high and low crime cities
        createCharts(data.topCities, data.lowestCities);
    })
    .catch(error => console.error('Error fetching data:', error));
}

function createCharts(topCities, lowestCities) {
    const highCrimeCtx = document.getElementById('high-crime-chart').getContext('2d');
    const lowCrimeCtx = document.getElementById('low-crime-chart').getContext('2d');

    const topCitiesNames = topCities.map(city => `${city.city}, ${city.state}`);
    const topCitiesCrimeRates = topCities.map(city => city.total_crimes);

    const lowestCitiesNames = lowestCities.map(city => `${city.city}, ${city.state}`);
    const lowestCitiesCrimeRates = lowestCities.map(city => city.total_crimes);

    // Check if data is properly fetched
    console.log('Top Cities:', topCities);
    console.log('Lowest Cities:', lowestCities);

    // Chart for high crime cities
    new Chart(highCrimeCtx, {
        type: 'bar',
        data: {
            labels: topCitiesNames,
            datasets: [{
                label: 'Reported Crimes (High Crime Cities)',
                data: topCitiesCrimeRates,
                backgroundColor: 'rgba(237, 31, 75, 0.54)',
                borderColor: 'rgb(145, 5, 36)',
                borderWidth: 1
            }]
        },
        options: {
            
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        display: false 
                    }
                }
            }
        }
    });

    // Chart for low crime cities
    new Chart(lowCrimeCtx, {
        type: 'bar',
        data: {
            labels: lowestCitiesNames,
            datasets: [{
                label: 'Reported Crimes (Low Crime Cities)',
                data: lowestCitiesCrimeRates,
                backgroundColor: 'rgba(54, 162, 234, 0.78)',
                borderColor: 'rgb(3, 58, 95)',
                borderWidth: 1
            }]
        },
        options: {
           
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        display: false 
                    }
                }
            }
        }
    });
}
