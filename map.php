<!DOCTYPE html>
<html>
<head>
    <title>India Map - Select Location</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        #map {
            width: 100%;
            height: 600px;
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

    <h2 style="text-align: center;">Select a Location in India</h2>
    <div id="map"></div>

    <script>
        // Initialize the map centered on India
        var map = L.map('map').setView([22.3511, 78.6677], 5);

        // Load OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Handle map click event
        map.on('click', function(e) {
            var lat = e.latlng.lat;
            var lon = e.latlng.lng;

            // Get location details using Nominatim API
            $.get(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`, function(data) {
                if (data && data.address) {
                    var state = data.address.state || "Unknown";
                    var city = data.address.city || data.address.town || data.address.village || "Unknown";

                    if (state !== "Unknown" && city !== "Unknown") {
                        if (confirm(`You selected:\nState: ${state}\nCity: ${city}\n\nProceed to crime prediction?`)) {
                            // Redirect to crime.php with selected state and city
                            window.location.href = `crime.php?state=${encodeURIComponent(state)}&city=${encodeURIComponent(city)}&autofill=true`;
                        }
                    } else {
                        alert("Please click on a more specific location.");
                    }
                } else {
                    alert("Location data not found. Try another area.");
                }
            });
        });
    </script>

</body>
</html>
