<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hourly-container {
            margin-top: 20px;
        }
        .hourly-item {
            background-color: #f8f9fa;
            color: #343a40;
            padding: 10px;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <section class="vh-100">
        <div class="container py-5">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-md-9 col-lg-7 col-xl-5">
                    <div class="card text-white bg-primary">
                        <div class="card-header p-4 border-0 text-center">
                            <h2 id="wrapper-name">Weather App</h2>
                            <input type="text" id="city-input" class="form-control" placeholder="Enter city name (e.g., Hyderabad, PK)" />
                            <button id="search-button" class="btn btn-light mt-3">Search</button>
                        </div>
                        <div class="card-body text-center">
                            <p id="wrapper-description"></p>
                            <h1 id="wrapper-temp"></h1>
                            <img id="weather-icon" src="" alt="Weather Icon" />
                            <p>Pressure: <span id="wrapper-pressure"></span></p>
                            <p>Humidity: <span id="wrapper-humidity"></span></p>
                            <p>Wind Speed: <span id="wrapper-wind"></span></p>
                        </div>
                        <div class="hourly-container" id="hourly-weather"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        const apiKey = "28114c687d72b7002e8f88e3e8456f30"; 

        // Fetch weather data function
        function fetchWeather(city) {
            const queryUrl = `https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}&units=metric`;

            fetch(queryUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const { main, weather, wind, coord } = data;
                    document.getElementById("wrapper-name").innerText = `${data.name}, ${data.sys.country}`;
                    document.getElementById("wrapper-description").innerText = weather[0].description.charAt(0).toUpperCase() + weather[0].description.slice(1);
                    document.getElementById("wrapper-temp").innerText = `${Math.round(main.temp)}°C`;
                    document.getElementById("wrapper-pressure").innerText = `${main.pressure} hPa`;
                    document.getElementById("wrapper-humidity").innerText = `${main.humidity}%`;
                    document.getElementById("wrapper-wind").innerText = `${wind.speed} m/s`;

                    // Set the weather icon
                    const iconCode = weather[0].icon;
                    document.getElementById("weather-icon").src = `https://openweathermap.org/img/wn/${iconCode}@2x.png`;

                    // Fetch hourly weather data
                    fetchHourlyWeather(coord.lat, coord.lon);
                })
                .catch(error => {
                    console.error("There was a problem with the fetch operation:", error);
                    alert("Failed to fetch weather data. Please check the console for more details.");
                });
        }

        // Fetch hourly weather data function
        function fetchHourlyWeather(lat, lon) {
            const hourlyUrl = `https://api.openweathermap.org/data/2.5/onecall?lat=${lat}&lon=${lon}&exclude=minutely,alerts&appid=${apiKey}&units=metric`;

            fetch(hourlyUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const hourlyWeather = data.hourly.slice(0, 5); // Get the next 5 hours
                    const hourlyContainer = document.getElementById("hourly-weather");
                    hourlyContainer.innerHTML = "<h4>Hourly Forecast</h4>"; // Title for hourly weather

                    hourlyWeather.forEach(hour => {
                        const time = new Date(hour.dt * 1000).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                        const temp = Math.round(hour.temp);
                        const description = hour.weather[0].description;
                        const iconCode = hour.weather[0].icon;

                        hourlyContainer.innerHTML += `
                            <div class="hourly-item">
                                <strong>${time}</strong>: ${temp}°C, ${description} 
                                <img src="https://openweathermap.org/img/wn/${iconCode}@2x.png" alt="Weather Icon" />
                            </div>
                        `;
                    });
                })
                .catch(error => {
                    console.error("There was a problem with the hourly fetch operation:", error);
                });
        }

        // Set default city
        const defaultCity = "Hyderabad, PK"; // Default city set to Hyderabad, Pakistan
        fetchWeather(defaultCity); // Fetch weather for default city

        document.getElementById("search-button").addEventListener("click", () => {
            const city = document.getElementById("city-input").value;
            fetchWeather(city);
        });

        document.getElementById("city-input").addEventListener("keydown", function(event) {
            if (event.key === "Enter") {
                const city = document.getElementById("city-input").value;
                fetchWeather(city);
            }
        });
    </script>
</body>
</html>
