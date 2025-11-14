<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Find Nearby Restaurants') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-6">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="radius" class="form-label">Search Radius (km)</label>
                            <input type="number" class="form-control" id="radius" value="10" min="1" max="50">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" class="btn btn-primary w-100" onclick="findNearbyMerchants()">
                                <i class="bi bi-search"></i> Find Nearby Restaurants
                            </button>
                        </div>
                    </div>
                    
                    <div id="userLocation" class="alert alert-info d-none">
                        <strong>Your Location:</strong> <span id="userCoords"></span>
                    </div>
                </div>
            </div>

            <!-- Map Display -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-6">
                    <div id="map" style="height: 500px; width: 100%;"></div>
                </div>
            </div>

            <!-- Results Display -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="mb-4">Nearby Restaurants</h3>
                    <div id="merchantsResults" class="row">
                        <div class="col-12 text-center text-muted">
                            Click "Find Nearby Restaurants" to see results
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let map = L.map('map').setView([-6.2088, 106.8456], 13); // Default to Jakarta
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        let userMarker = null;
        let merchantMarkers = [];
        let userLocation = null;

        // Custom icons
        const userIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        const merchantIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        function findNearbyMerchants() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    userLocation = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude
                    };

                    // Show user location
                    document.getElementById('userLocation').classList.remove('d-none');
                    document.getElementById('userCoords').textContent = 
                        `${userLocation.latitude.toFixed(6)}, ${userLocation.longitude.toFixed(6)}`;

                    // Add user marker
                    if (userMarker) {
                        map.removeLayer(userMarker);
                    }
                    userMarker = L.marker([userLocation.latitude, userLocation.longitude], {icon: userIcon})
                        .addTo(map)
                        .bindPopup('<strong>You are here</strong>')
                        .openPopup();

                    map.setView([userLocation.latitude, userLocation.longitude], 13);

                    // Fetch nearby merchants
                    const radius = document.getElementById('radius').value;
                    fetch(`/api/merchants/nearby?latitude=${userLocation.latitude}&longitude=${userLocation.longitude}&radius=${radius}`, {
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        displayMerchants(data.merchants);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error fetching nearby merchants');
                    });
                }, function(error) {
                    alert('Error getting location: ' + error.message);
                });
            } else {
                alert('Geolocation is not supported by this browser.');
            }
        }

        function displayMerchants(merchants) {
            // Clear previous merchant markers
            merchantMarkers.forEach(marker => map.removeLayer(marker));
            merchantMarkers = [];

            const resultsDiv = document.getElementById('merchantsResults');
            
            if (merchants.length === 0) {
                resultsDiv.innerHTML = '<div class="col-12 text-center text-muted">No restaurants found nearby</div>';
                return;
            }

            let html = '';
            merchants.forEach(merchant => {
                // Add marker to map
                if (merchant.latitude && merchant.longitude) {
                    const marker = L.marker([merchant.latitude, merchant.longitude], {icon: merchantIcon})
                        .addTo(map)
                        .bindPopup(`
                            <strong>${merchant.name}</strong><br>
                            ${merchant.address}<br>
                            Distance: ${merchant.formatted_distance}
                        `);
                    merchantMarkers.push(marker);
                }

                // Add to results list
                html += `
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            ${merchant.image ? 
                                `<img src="/storage/${merchant.image}" class="card-img-top" style="height: 200px; object-fit: cover;">` :
                                '<div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;"><span>No Image</span></div>'
                            }
                            <div class="card-body">
                                <h5 class="card-title">${merchant.name}</h5>
                                <p class="card-text"><small class="text-muted">${merchant.address}</small></p>
                                <p class="card-text">
                                    <span class="badge bg-primary">
                                        <i class="bi bi-geo-alt"></i> ${merchant.formatted_distance}
                                    </span>
                                </p>
                                <a href="/merchants/${merchant.id}" class="btn btn-sm btn-primary">View Menu</a>
                            </div>
                        </div>
                    </div>
                `;
            });

            resultsDiv.innerHTML = html;
        }

        // Load all merchants on page load
        window.addEventListener('load', function() {
            fetch('/api/merchants/all', {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                data.merchants.forEach(merchant => {
                    if (merchant.latitude && merchant.longitude) {
                        L.marker([merchant.latitude, merchant.longitude], {icon: merchantIcon})
                            .addTo(map)
                            .bindPopup(`<strong>${merchant.name}</strong><br>${merchant.address}`);
                    }
                });
            });
        });
    </script>
</x-app-layout>
